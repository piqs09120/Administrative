<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;


class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $role = auth()->user()->role ?? null;
            if (!in_array(strtolower($role), ['administrator'])) {
                abort(403, 'Only Administrators can approve or deny document releases.');
            }
            return $next($request);
        })->only(['approve', 'deny']);
    }

    public function index()
    {
        $documents = Document::with('uploader')->latest()->get();
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
            'document_file' => 'required|file|mimes:pdf,doc,docx,txt,jpg,jpeg,png|max:10240'
        ]);

        $file = $request->file('document_file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents', $fileName, 'public');

        // Extract text for .txt files only (for demo)
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
        // For PDF/DOCX, you need to use a parser package

        // Call Python API if text is available
        $category = null;
        $entities = [];
        if ($documentText) {
            $response = \Illuminate\Support\Facades\Http::post('http://127.0.0.1:5050/classify', [
                'text' => $documentText,
            ]);
            $category = $response->json('category');
            $entities = $response->json('entities');
        }

        $document = Document::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
            'uploaded_by' => \Illuminate\Support\Facades\Auth::id(),
            'status' => 'archived',
            'category' => $category, // Save the category
        ]);

        // Pass entities to the show view after upload
        return redirect()->route('document.show', $document->id);
    }

    public function show($id)
    {
        $document = Document::with(['uploader', 'documentRequests.requester', 'documentRequests.approver'])->findOrFail($id);
        $entities = [];
        // Try to extract text and get entities if file exists
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
        return view('document.show', compact('document', 'entities'));
    }

    public function edit($id)
    {
        $document = Document::findOrFail($id);
        return view('document.edit', compact('document'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $document = Document::findOrFail($id);
        $document->update([
            'title' => $request->title,
            'description' => $request->description
        ]);

        return redirect()->route('document.show', $id)->with('success', 'Document updated successfully!');
    }

    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        
        // Delete file from storage
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }
        
        $document->delete();
        return redirect()->route('document.index')->with('success', 'Document deleted successfully!');
    }

    public function requestRelease($id)
    {
        $document = Document::findOrFail($id);
        
        // Check if document is archived
        if ($document->status !== 'archived') {
            return redirect()->back()->with('error', 'Dxocument is not available for release request.');
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
        $document = Document::findOrFail($id);
        
        if (!Storage::disk('public')->exists($document->file_path)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return Storage::disk('public')->download($document->file_path);
    }
} 