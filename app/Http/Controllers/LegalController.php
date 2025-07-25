<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FacilityReservation;
use App\Notifications\DocumentRequestStatusNotification;
use App\Models\AccessLog;
use Illuminate\Support\Facades\Http;


class LegalController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || strtolower(auth()->user()->role) !== 'administrator') {
                abort(403, 'Only Administrators can access this section.');
            }
            return $next($request);
        })->only(['index', 'approveRequest', 'denyRequest']);
    }

    public function index()
    {
        $pendingRequests = DocumentRequest::with(['document.uploader', 'requester'])
            ->where('status', 'pending')
            ->latest()
            ->get();
            
        $approvedRequests = DocumentRequest::with(['document.uploader', 'requester', 'approver'])
            ->where('status', 'approved')
            ->latest()
            ->take(10)
            ->get();
            
        $deniedRequests = DocumentRequest::with(['document.uploader', 'requester', 'approver'])
            ->where('status', 'denied')
            ->latest()
            ->take(10)
            ->get();

        // Facility reservations
        $pendingFacilityReservations = FacilityReservation::with(['facility', 'reserver', 'approver'])
            ->where('status', 'pending')
            ->latest()
            ->get();
        $approvedFacilityReservations = FacilityReservation::with(['facility', 'reserver', 'approver'])
            ->where('status', 'approved')
            ->latest()
            ->take(10)
            ->get();
        $deniedFacilityReservations = FacilityReservation::with(['facility', 'reserver', 'approver'])
            ->where('status', 'denied')
            ->latest()
            ->take(10)
            ->get();

        return view('legal.index', compact(
            'pendingRequests', 'approvedRequests', 'deniedRequests',
            'pendingFacilityReservations', 'approvedFacilityReservations', 'deniedFacilityReservations'
        ));
    }

    public function create()
    {
        return view('legal.create');
    }

    public function store(Request $request)
    {
        // Legal doesn't create documents, only approves requests
        return redirect()->route('legal.index');
    }

    public function show($id)
    {
        $request = DocumentRequest::with(['document.uploader', 'requester', 'approver'])->findOrFail($id);
        $entities = [];
        $document = $request->document;
        if ($document) {
            $filePath = storage_path('app/public/' . $document->file_path);
            if (file_exists($filePath)) {
                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                $documentText = '';
                if ($extension === 'txt') {
                    $documentText = file_get_contents($filePath);
                } elseif ($extension === 'pdf') {
                    $parser = new \Smalot\PdfParser\Parser();
                    $pdf = $parser->parseFile($filePath);
                    $documentText = $pdf->getText();
                } elseif ($extension === 'docx') {
                    $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
                    $text = '';
                    foreach ($phpWord->getSections() as $section) {
                        $elements = $section->getElements();
                        foreach ($elements as $element) {
                            if (method_exists($element, 'getText')) {
                                $text .= $element->getText() . ' ';
                            }
                        }
                    }
                    $documentText = $text;
                }
                if ($documentText) {
                    $response = \Illuminate\Support\Facades\Http::post('http://127.0.0.1:5050/classify', [
                        'text' => $documentText,
                    ]);
                    $entities = $response->json('entities');
                }
            }
        }
        return view('legal.show', compact('request', 'entities'));
    }

    public function edit($id)
    {
        $request = DocumentRequest::with(['document.uploader', 'requester'])->findOrFail($id);
        return view('legal.edit', compact('request'));
    }

    public function update(Request $request, $id)
    {
        // This would be for editing legal notes or remarks
        return redirect()->route('legal.index');
    }

    public function destroy($id)
    {
        // Legal doesn't delete requests, only approves/denies
        return redirect()->route('legal.index');
    }

    public function approveRequest($id)
    {
        $documentRequest = DocumentRequest::findOrFail($id);
        
        if ($documentRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'This request has already been processed.');
        }

        // Extract text and classify document
        $document = $documentRequest->document;
        $filePath = storage_path('app/public/' . $document->file_path);
        $category = null;
        if (file_exists($filePath)) {
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $documentText = '';
            if ($extension === 'txt') {
                $documentText = file_get_contents($filePath);
            } elseif ($extension === 'pdf') {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($filePath);
                $documentText = $pdf->getText();
            } elseif ($extension === 'docx') {
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
                $text = '';
                foreach ($phpWord->getSections() as $section) {
                    $elements = $section->getElements();
                    foreach ($elements as $element) {
                        if (method_exists($element, 'getText')) {
                            $text .= $element->getText() . ' ';
                        }
                    }
                }
                $documentText = $text;
            }
            if ($documentText) {
                $response = \Illuminate\Support\Facades\Http::post('http://127.0.0.1:5050/classify', [
                    'text' => $documentText,
                ]);
                $category = $response->json('category');
            }
        }
        if ($category) {
            $document->update(['category' => $category]);
        }

        $documentRequest->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'remarks' => request('remarks')
        ]);

        // Update document status to released
        $documentRequest->document->update(['status' => 'released']);

        // Notify requester
        $documentRequest->requester->notify(new DocumentRequestStatusNotification($documentRequest));

        // Log action
        AccessLog::create([
            'user_id' => Auth::id(),
            'action' => 'approve_document_request',
            'description' => 'Approved document request ID ' . $documentRequest->id,
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('legal.index')->with('success', 'Document release request approved successfully!');
    }

    public function denyRequest($id)
    {
        $documentRequest = DocumentRequest::findOrFail($id);
        
        if ($documentRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'This request has already been processed.');
        }

        $documentRequest->update([
            'status' => 'denied',
            'approved_by' => Auth::id(),
            'remarks' => request('remarks')
        ]);

        // Notify requester
        $documentRequest->requester->notify(new DocumentRequestStatusNotification($documentRequest));

        // Log action
        AccessLog::create([
            'user_id' => Auth::id(),
            'action' => 'deny_document_request',
            'description' => 'Denied document request ID ' . $documentRequest->id,
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('legal.index')->with('success', 'Document release request denied.');
    }

    public function pendingRequests()
    {
        $pendingRequests = DocumentRequest::with(['document.uploader', 'requester'])
            ->where('status', 'pending')
            ->latest()
            ->get();
            
        return view('legal.pending', compact('pendingRequests'));
    }

    public function approvedRequests()
    {
        $approvedRequests = DocumentRequest::with(['document.uploader', 'requester', 'approver'])
            ->where('status', 'approved')
            ->latest()
            ->paginate(20);
            
        return view('legal.approved', compact('approvedRequests'));
    }

    public function deniedRequests()
    {
        $deniedRequests = DocumentRequest::with(['document.uploader', 'requester', 'approver'])
            ->where('status', 'denied')
            ->latest()
            ->paginate(20);
            
        return view('legal.denied', compact('deniedRequests'));
    }
} 