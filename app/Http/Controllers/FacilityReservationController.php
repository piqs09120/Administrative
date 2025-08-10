<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\FacilityReservation;
use App\Services\GeminiService;
use App\Services\DocumentTextExtractorService;
use App\Services\FacilityCalendarService;
use App\Services\SecureDocumentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Notifications\FacilityReservationStatusNotification;
use App\Models\AccessLog;
use App\Jobs\ProcessReservationDocument;
use App\Jobs\CheckAndAutoApproveReservation;
use App\Jobs\GenerateDigitalPasses;

class FacilityReservationController extends Controller
{
    protected $geminiService;
    protected $textExtractor;
    protected $calendarService;
    protected $secureRepository;

    public function __construct(GeminiService $geminiService, DocumentTextExtractorService $textExtractor, FacilityCalendarService $calendarService, SecureDocumentRepository $secureRepository)
    {
        $this->geminiService = $geminiService;
        $this->textExtractor = $textExtractor;
        $this->calendarService = $calendarService;
        $this->secureRepository = $secureRepository;
        
        // Role restrictions removed - all users can now approve/deny reservations
    }

    public function index()
    {
        // Show all reservations to all users - role restrictions removed
        $reservations = FacilityReservation::with(['facility', 'reserver', 'approver'])->latest()->get();
        return view('facility_reservations.index', compact('reservations'));
    }

    public function create()
    {
        $facilities = Facility::where('status', 'available')->get();
        return view('facility_reservations.create', compact('facilities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'purpose' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx,txt,jpg,jpeg,png|max:10240' // 10MB max
        ]);

        // Step 1: Create the reservation
        $reservation = FacilityReservation::create([
            'facility_id' => $request->facility_id,
            'reserved_by' => Auth::id(),
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'purpose' => $request->purpose,
            'status' => 'pending',
            // Auto-populate requester information from authenticated user
            'requester_name' => Auth::user()->name,
            'requester_department' => Auth::user()->department ?? 'Not specified',
            'requester_contact' => Auth::user()->email,
            'workflow_stage' => 'submitted'
        ]);
        
        // Initialize workflow log
        $reservation->logWorkflowStep('submitted', 'Facility reservation request submitted', [
            'facility' => $reservation->facility->name,
            'requester' => Auth::user()->name
        ]);

        // Step 2: Handle document upload if provided (following TO BE diagram)
        if ($request->hasFile('document')) {
            // Upload supporting document
            $reservation->logWorkflowStep('document_upload_started', 'Starting document upload process');
            
            $documentPath = $this->handleDocumentUpload($request->file('document'), $reservation->id);
            $reservation->update(['document_path' => $documentPath]);
            
            // Store document in secure repository
            $storeResult = $this->secureRepository->storeDocument(
                $request->file('document'), 
                $reservation->id
            );
            
            if ($storeResult['success']) {
                $reservation->logWorkflowStep('document_stored_secure_repository', 
                    'Document stored and indexed in secure repository', $storeResult['index']);
            }
            
            // Send document to Gemini AI for classification (queued in background)
            $reservation->logWorkflowStep('document_sent_to_ai', 'Document sent to Gemini AI for classification');
            \App\Jobs\ProcessReservationDocument::dispatch($reservation->id);
        }

        // Step 3: If no document uploaded, proceed directly to approval workflow
        if (!$request->hasFile('document')) {
            // No document = NO to "Does document require legal validation?" 
            // → Proceed to approval → Auto check facility availability → Auto approve
            $reservation->logWorkflowStep('no_document_proceed_to_approval', 
                'No document uploaded, proceeding directly to auto-approval workflow');
            \App\Jobs\CheckAndAutoApproveReservation::dispatch($reservation->id);
        }

        // Send submission confirmation (status pending) with error handling
        try {
            $reservation->reserver->notify(new FacilityReservationStatusNotification($reservation));
        } catch (\Exception $e) {
            Log::warning('Failed to send submission confirmation email', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->route('facility_reservations.index')
            ->with('success', 'Reservation request submitted! The system will automatically check facility availability and process your request.');
    }

    private function handleDocumentUpload($file, $reservationId)
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('facility_reservations/' . $reservationId, $fileName, 'public');
        return $path;
    }

    private function processDocumentWithAI($reservation)
    {
        try {
            // Get document content
            $documentPath = storage_path('app/public/' . $reservation->document_path);
            
            if (!file_exists($documentPath)) {
                throw new \Exception('Document file not found');
            }

            // Extract text from the file using our service
            $content = $this->textExtractor->extractText($documentPath);
            
            // Send to Gemini AI for analysis
            $aiResult = $this->geminiService->analyzeDocument($content);
            
            if (!$aiResult['error']) {
                // Store AI classification results
                $reservation->update([
                    'ai_classification' => $aiResult,
                    'requires_legal_review' => $this->requiresLegalReview($aiResult),
                    'requires_visitor_coordination' => $this->requiresVisitorCoordination($aiResult)
                ]);

                Log::info('AI analysis completed for reservation ' . $reservation->id, $aiResult);
            } else {
                $reservation->update([
                    'ai_error' => $aiResult['message']
                ]);
                Log::error('AI analysis failed for reservation ' . $reservation->id, $aiResult);
            }

        } catch (\Exception $e) {
            $reservation->update([
                'ai_error' => 'Document processing failed: ' . $e->getMessage()
            ]);
            Log::error('Document processing failed for reservation ' . $reservation->id, [
                'error' => $e->getMessage()
            ]);
        }
    }



    private function requiresLegalReview($aiResult)
    {
        $legalCategories = ['contract', 'subpoena', 'affidavit', 'cease_desist', 'legal_notice'];
        $category = $aiResult['category'] ?? 'general';
        
        return in_array($category, $legalCategories);
    }

    private function requiresVisitorCoordination($aiResult)
    {
        // Check if the document contains visitor-related keywords
        $visitorKeywords = ['visitor', 'guest', 'attendee', 'participant', 'delegate'];
        $summary = strtolower($aiResult['summary'] ?? '');
        $keyInfo = strtolower($aiResult['key_info'] ?? '');
        
        foreach ($visitorKeywords as $keyword) {
            if (strpos($summary, $keyword) !== false || strpos($keyInfo, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    private function checkAvailabilityAndAutoApprove($reservation)
    {
        try {
            // Check if facility is available for the requested time
            $conflictingReservations = FacilityReservation::where('facility_id', $reservation->facility_id)
                ->where('status', '!=', 'denied')
                ->where(function ($query) use ($reservation) {
                    $query->whereBetween('start_time', [$reservation->start_time, $reservation->end_time])
                          ->orWhereBetween('end_time', [$reservation->start_time, $reservation->end_time])
                          ->orWhere(function ($q) use ($reservation) {
                              $q->where('start_time', '<=', $reservation->start_time)
                                ->where('end_time', '>=', $reservation->end_time);
                          });
                })
                ->where('id', '!=', $reservation->id)
                ->count();

            if ($conflictingReservations === 0) {
                // No conflicts, check if auto-approval is possible
                if (!$reservation->requires_legal_review && !$reservation->requires_visitor_coordination) {
                    // Auto-approve the reservation
                    $reservation->update([
                        'status' => 'approved',
                        'approved_by' => null, // System approval
                        'auto_approved_at' => now(),
                        'remarks' => 'Auto-approved by system - no conflicts and no special requirements'
                    ]);

                    // Send notification to user
                    $reservation->reserver->notify(new FacilityReservationStatusNotification($reservation));

                    Log::info('Reservation auto-approved', ['reservation_id' => $reservation->id]);
                } else {
                    // Requires review, but facility is available
                    $reservation->update([
                        'remarks' => 'Facility available but requires ' . 
                                   ($reservation->requires_legal_review ? 'legal review' : '') .
                                   ($reservation->requires_legal_review && $reservation->requires_visitor_coordination ? ' and ' : '') .
                                   ($reservation->requires_visitor_coordination ? 'visitor coordination' : '')
                    ]);
                }
            } else {
                // Conflicts found
                $reservation->update([
                    'status' => 'denied',
                    'remarks' => 'Facility not available for requested time period'
                ]);

                Log::info('Reservation denied due to conflicts', ['reservation_id' => $reservation->id]);
            }

        } catch (\Exception $e) {
            Log::error('Error checking availability for reservation ' . $reservation->id, [
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        $reservation = FacilityReservation::with(['facility', 'reserver', 'approver'])->findOrFail($id);
        return view('facility_reservations.show', compact('reservation'));
    }

    public function approve($id)
    {
        // Role restrictions removed - all users can approve reservations
        $reservation = FacilityReservation::findOrFail($id);
        
        if ($reservation->status !== 'pending') {
            return redirect()->back()->with('error', 'This reservation has already been processed.');
        }
        
        $reservation->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'remarks' => request('remarks')
        ]);
        
        // Notify reserver
        $reservation->reserver->notify(new FacilityReservationStatusNotification($reservation));
        
        // Log action
        AccessLog::create([
            'user_id' => Auth::id(),
            'action' => 'approve_facility_reservation',
            'description' => 'Approved facility reservation ID ' . $reservation->id,
            'ip_address' => request()->ip()
        ]);
        
        return redirect()->route('facility_reservations.index')->with('success', 'Reservation approved!');
    }

    public function deny($id)
    {
        // Role restrictions removed - all users can deny reservations
        $reservation = FacilityReservation::findOrFail($id);
        
        if ($reservation->status !== 'pending') {
            return redirect()->back()->with('error', 'This reservation has already been processed.');
        }
        
        $reservation->update([
            'status' => 'denied',
            'approved_by' => Auth::id(),
            'remarks' => request('remarks')
        ]);
        
        // Notify reserver
        $reservation->reserver->notify(new FacilityReservationStatusNotification($reservation));
        
        // Log action
        AccessLog::create([
            'user_id' => Auth::id(),
            'action' => 'deny_facility_reservation',
            'description' => 'Denied facility reservation ID ' . $reservation->id,
            'ip_address' => request()->ip()
        ]);
        
        return redirect()->route('facility_reservations.index')->with('success', 'Reservation denied.');
    }

    public function legalReview($id)
    {
        // Role restrictions removed - all users can perform legal review
        $reservation = FacilityReservation::with(['facility', 'reserver', 'approver'])->findOrFail($id);
        
        if (!$reservation->requires_legal_review) {
            return redirect()->back()->with('error', 'This reservation does not require legal review.');
        }
        
        return view('facility_reservations.legal_review', compact('reservation'));
    }

    public function legalApprove($id)
    {
        // Role restrictions removed - all users can perform legal review
        $reservation = FacilityReservation::findOrFail($id);
        
        if (!$reservation->requires_legal_review) {
            return redirect()->back()->with('error', 'This reservation does not require legal review.');
        }
        
        $reservation->update([
            'legal_reviewed_by' => Auth::id(),
            'legal_reviewed_at' => now(),
            'legal_comment' => request('legal_comment'),
            'requires_legal_review' => false // Clear the flag
        ]);
        
        // Check if we can now auto-approve (no more special requirements)
        if (!$reservation->requires_visitor_coordination && $reservation->status === 'pending') {
            $reservation->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'remarks' => 'Approved after legal review completion'
            ]);
            
            // Notify reserver
            $reservation->reserver->notify(new FacilityReservationStatusNotification($reservation));
        }
        
        // Log action
        AccessLog::create([
            'user_id' => Auth::id(),
            'action' => 'legal_approve_facility_reservation',
            'description' => 'Legal approved facility reservation ID ' . $reservation->id,
            'ip_address' => request()->ip()
        ]);
        
        return redirect()->route('facility_reservations.show', $id)->with('success', 'Legal review completed successfully!');
    }

    public function legalFlag($id)
    {
        // Role restrictions removed - all users can perform legal review
        $reservation = FacilityReservation::findOrFail($id);
        
        if (!$reservation->requires_legal_review) {
            return redirect()->back()->with('error', 'This reservation does not require legal review.');
        }
        
        $reservation->update([
            'status' => 'denied',
            'approved_by' => Auth::id(),
            'legal_reviewed_by' => Auth::id(),
            'legal_reviewed_at' => now(),
            'legal_comment' => request('legal_comment'),
            'remarks' => 'Flagged by legal review: ' . request('legal_comment')
        ]);
        
        // Notify reserver
        $reservation->reserver->notify(new FacilityReservationStatusNotification($reservation));
        
        // Log action
        AccessLog::create([
            'user_id' => Auth::id(),
            'action' => 'legal_flag_facility_reservation',
            'description' => 'Legal flagged facility reservation ID ' . $reservation->id,
            'ip_address' => request()->ip()
        ]);
        
        return redirect()->route('facility_reservations.show', $id)->with('success', 'Reservation flagged by legal review.');
    }

    public function extractVisitorData($id)
    {
        $reservation = FacilityReservation::findOrFail($id);
        
        if (!$reservation->requires_visitor_coordination) {
            return redirect()->back()->with('error', 'This reservation does not require visitor coordination.');
        }
        
        if (!$reservation->ai_classification) {
            return redirect()->back()->with('error', 'No AI classification data available for visitor extraction.');
        }
        
        $visitorData = $this->parseVisitorDataFromAI($reservation->ai_classification);
        
        if (empty($visitorData)) {
            return redirect()->back()->with('error', 'No visitor data could be extracted from the document.');
        }
        
        $reservation->update([
            'visitor_data' => $visitorData
        ]);
        
        return redirect()->route('facility_reservations.show', $id)->with('success', 'Visitor data extracted successfully!');
    }

    private function parseVisitorDataFromAI($aiClassification)
    {
        $visitors = [];
        $keyInfo = $aiClassification['key_info'] ?? '';
        $summary = $aiClassification['summary'] ?? '';
        
        // Simple extraction logic - in real implementation, this would be more sophisticated
        $text = $keyInfo . ' ' . $summary;
        
        // Look for patterns like "Name: John Doe", "Visitor: Jane Smith", etc.
        $patterns = [
            '/(?:visitor|guest|attendee|participant|delegate):\s*([A-Za-z\s]+)/i',
            '/name:\s*([A-Za-z\s]+)/i',
            '/([A-Z][a-z]+\s+[A-Z][a-z]+)(?:\s+will\s+visit|is\s+visiting|to\s+attend)/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $text, $matches)) {
                foreach ($matches[1] as $match) {
                    $name = trim($match);
                    if (strlen($name) > 3 && !in_array($name, $visitors)) {
                        $visitors[] = [
                            'name' => $name,
                            'status' => 'pending_approval',
                            'extracted_at' => now()->toISOString()
                        ];
                    }
                }
            }
        }
        
        return $visitors;
    }

    public function approveVisitors($id)
    {
        $reservation = FacilityReservation::findOrFail($id);
        
        if (!$reservation->requires_visitor_coordination || !$reservation->visitor_data) {
            return redirect()->back()->with('error', 'No visitors to approve for this reservation.');
        }
        
        $visitorData = $reservation->visitor_data;
        
        // Update visitor status to approved
        foreach ($visitorData as &$visitor) {
            $visitor['status'] = 'approved';
            $visitor['approved_at'] = now()->toISOString();
            $visitor['approved_by'] = Auth::id();
        }
        
        $reservation->update([
            'visitor_data' => $visitorData,
            'requires_visitor_coordination' => false // Clear the flag
        ]);
        
        // Generate digital passes and notify security
        $this->generateDigitalPasses($reservation);
        
        // Generate digital passes for approved visitors
        GenerateDigitalPasses::dispatch($reservation->id);
        
        // Check if we can now auto-approve (no more special requirements)
        if (!$reservation->requires_legal_review && $reservation->status === 'pending') {
            $reservation->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'remarks' => 'Approved after visitor coordination completion'
            ]);
            
            $reservation->updateWorkflowStage('approved', 'Approved after visitor coordination');
            
            // Notify reserver
            $reservation->reserver->notify(new FacilityReservationStatusNotification($reservation));
        }
        
        return redirect()->route('facility_reservations.show', $id)->with('success', 'Visitors approved! Digital passes are being generated and security team will be notified.');
    }

    public function calendar($facilityId = null, Request $request)
    {
        $facilities = Facility::where('status', 'available')->get();
        $selectedFacility = $facilityId ? Facility::findOrFail($facilityId) : $facilities->first();
        
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        $calendar = [];
        if ($selectedFacility) {
            $calendar = $this->calendarService->getFacilityCalendar($selectedFacility->id, $startDate, $endDate);
        }
        
        return view('facility_reservations.calendar', compact('facilities', 'selectedFacility', 'calendar', 'startDate', 'endDate'));
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time'
        ]);

        $availability = $this->calendarService->checkAvailability(
            $request->facility_id,
            $request->start_time,
            $request->end_time,
            $request->exclude_reservation_id
        );

        return response()->json($availability);
    }

    private function generateDigitalPasses($reservation)
    {
        // In a real implementation, this would generate actual digital passes
        // For now, we'll just log the action and potentially send notifications
        
        Log::info('Digital passes generated for reservation', [
            'reservation_id' => $reservation->id,
            'visitors' => $reservation->visitor_data,
            'facility' => $reservation->facility->name,
            'date_range' => $reservation->start_time . ' to ' . $reservation->end_time
        ]);
        
        // Here you could:
        // 1. Generate QR codes for each visitor
        // 2. Send email notifications to visitors with their passes
        // 3. Notify security personnel
        // 4. Update building access systems
        
        // For demonstration, let's create a simple notification
        // You could create a new notification class for this
    }

    public function destroy($id)
    {
        $reservation = FacilityReservation::findOrFail($id);
        
        // Delete associated document if exists
        if ($reservation->document_path) {
            Storage::disk('public')->delete($reservation->document_path);
        }
        
        $reservation->delete();
        return redirect()->route('facility_reservations.index')->with('success', 'Reservation deleted.');
    }
}
