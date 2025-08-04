<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentRequest;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


class DocumentController extends Controller
{
    public function __construct()
    {
        // Removed administrator-only restriction
    }

    public function index()
    {
        $documents = Document::with('uploader')
            ->where('source', 'document_management')
            ->latest()
            ->get();
        return view('document.index', compact('documents'));
    }

    public function create()
    {
        return view('document.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:255',
            'document_file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:10240'
        ]);

        $file = $request->file('document_file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents', $fileName, 'public');

        // Extract text from document for AI analysis
        $documentText = '';
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'txt') {
            $documentText = file_get_contents($file->getRealPath());
        } elseif ($extension === 'pdf') {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($file->getRealPath());
            $documentText = $pdf->getText();
        } elseif ($extension === 'docx') {
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($file->getRealPath());
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

        // Use Gemini AI for document analysis
        $category = $request->ai_category ?: $request->category; // Use AI category if available
        $aiAnalysis = null;
        
        if ($documentText) {
            try {
                $geminiService = new GeminiService();
                $aiAnalysis = $geminiService->analyzeDocument($documentText);
                
                if (!$aiAnalysis['error']) {
                    $category = $aiAnalysis['category'];
                }
            } catch (\Exception $e) {
                // Log error but continue with document creation
                \Log::error('Gemini AI analysis failed: ' . $e->getMessage());
                $aiAnalysis = null;
            }
        }

        $document = Document::create([
            'title' => $request->title,
            'description' => $request->description,
            'department' => $request->department,
            'category' => $category,
            'author' => $request->author,
            'file_path' => $filePath,
            'uploaded_by' => \Illuminate\Support\Facades\Auth::id(),
            'status' => 'archived',
            'source' => 'document_management', // Mark as created through Document Management
        ]);

        // Store AI analysis data if available
        if ($aiAnalysis && !$aiAnalysis['error']) {
            $document->update([
                'ai_analysis' => json_encode($aiAnalysis)
            ]);
        }

        // Pass entities to the show view after upload
        return redirect()->route('document.show', $document->id)->with('success', 'Document uploaded and analyzed successfully!');
    }

    public function show($id)
    {
        $document = Document::with(['uploader', 'documentRequests.requester', 'documentRequests.approver'])
            ->where('source', 'document_management')
            ->findOrFail($id);
        return view('document.show', compact('document'));
    }

    public function edit($id)
    {
        $document = Document::where('source', 'document_management')->findOrFail($id);
        return view('document.edit', compact('document'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $document = Document::where('source', 'document_management')->findOrFail($id);
        $document->update([
            'title' => $request->title,
            'description' => $request->description
        ]);

        return redirect()->route('document.show', $id)->with('success', 'Document updated successfully!');
    }

    public function destroy($id)
    {
        $document = Document::where('source', 'document_management')->findOrFail($id);
        
        // Delete file from storage
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }
        
        $document->delete();
        return redirect()->route('document.index')->with('success', 'Document deleted successfully!');
    }

    public function requestRelease($id)
    {
        $document = Document::where('source', 'document_management')->findOrFail($id);
        
        // Check if document is archived
        if ($document->status !== 'archived') {
            return redirect()->back()->with('error', 'Document is not available for release request.');
        }

        // Check if there's already a pending request
        $existingRequest = DocumentRequest::where('document_id', $id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return redirect()->back()->with('error', 'A release request is already pending for this document.');
        }

        DocumentRequest::create([
            'document_id' => $id,
            'requested_by' => Auth::id(),
            'status' => 'pending'
        ]);

        $document->update(['status' => 'pending_release']);

        return redirect()->route('document.show', $id)->with('success', 'Release request submitted successfully!');
    }

    public function download($id)
    {
        $document = Document::where('source', 'document_management')->findOrFail($id);
        
        if (!Storage::disk('public')->exists($document->file_path)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return Storage::disk('public')->download($document->file_path);
    }

    public function analyze($id)
    {
        $document = Document::where('source', 'document_management')->findOrFail($id);
        
        // Extract text from document
        $filePath = storage_path('app/public/' . $document->file_path);
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $documentText = '';
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

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

        if (!$documentText) {
            return redirect()->back()->with('error', 'Could not extract text from document.');
        }

        try {
            $geminiService = new GeminiService();
            $aiAnalysis = $geminiService->analyzeDocument($documentText);
            
            if ($aiAnalysis['error']) {
                return redirect()->back()->with('error', 'AI analysis failed: ' . $aiAnalysis['message']);
            }

            // Update document with AI analysis
            $document->update([
                'ai_analysis' => $aiAnalysis,
                'category' => $aiAnalysis['category']
            ]);

            return redirect()->route('document.show', $id)->with('success', 'Document analyzed successfully!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'AI analysis failed: ' . $e->getMessage());
        }
    }

    public function analyzeUpload(Request $request)
    {
        $request->validate([
            'document_file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:10240'
        ]);

        $file = $request->file('document_file');
        $documentText = '';
        $extension = strtolower($file->getClientOriginalExtension());

        // Extract text from uploaded file
        if ($extension === 'txt') {
            $documentText = file_get_contents($file->getRealPath());
        } elseif ($extension === 'pdf') {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($file->getRealPath());
            $documentText = $pdf->getText();
        } elseif ($extension === 'docx') {
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($file->getRealPath());
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

        if (!$documentText) {
            return response()->json([
                'success' => false,
                'message' => 'Could not extract text from document.'
            ]);
        }

        try {
            $geminiService = new GeminiService();
            $aiAnalysis = $geminiService->analyzeDocument($documentText);
            
            // Check if analysis was successful
            if (isset($aiAnalysis['error']) && $aiAnalysis['error']) {
                return response()->json([
                    'success' => false,
                    'message' => 'AI analysis failed: ' . ($aiAnalysis['message'] ?? 'Unknown error')
                ]);
            }

            return response()->json([
                'success' => true,
                'analysis' => $aiAnalysis
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI analysis failed: ' . $e->getMessage()
            ]);
        }
    }
} 