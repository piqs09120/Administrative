<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentAccessLog;
use App\Models\AccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentAccessController extends Controller
{
    /**
     * Track document access and enforce confidentiality rules
     */
    public function trackAccess(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        $user = Auth::user();
        
        // Check access permissions
        if (!$this->canAccessDocument($user, $document)) {
            return response()->json([
                'error' => 'Access Denied',
                'message' => 'You do not have permission to access this document.'
            ], 403);
        }

        // Log the access
        $this->logDocumentAccess($document, $user, 'view');
        
        return response()->json([
            'success' => true,
            'message' => 'Access granted',
            'document' => [
                'id' => $document->id,
                'title' => $document->title,
                'confidentiality' => $document->confidentiality,
                'access_level' => $this->getAccessLevel($user, $document)
            ]
        ]);
    }

    /**
     * Download document with access control
     */
    public function download(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        $user = Auth::user();
        
        // Check access permissions
        if (!$this->canAccessDocument($user, $document)) {
            return response()->json([
                'error' => 'Access Denied',
                'message' => 'You do not have permission to download this document.'
            ], 403);
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($document->file_path)) {
            return response()->json([
                'error' => 'File Not Found',
                'message' => 'The document file could not be found.'
            ], 404);
        }

        // Log the download
        $this->logDocumentAccess($document, $user, 'download');
        
        // Return file download
        return response()->download(storage_path('app/public/' . $document->file_path), $document->title);
    }

    /**
     * Get document access analytics
     */
    public function getAccessAnalytics(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        $user = Auth::user();
        
        // Only administrators can view access analytics
        if (!in_array($user->role ?? 'user', ['admin', 'Administrator', 'super_admin', 'legal_admin'])) {
            return response()->json([
                'error' => 'Access Denied',
                'message' => 'You do not have permission to view access analytics.'
            ], 403);
        }

        $analytics = $this->generateAccessAnalytics($document);
        
        return response()->json([
            'success' => true,
            'analytics' => $analytics
        ]);
    }

    /**
     * Check if user can access document
     */
    private function canAccessDocument($user, Document $document)
    {
        $confidentiality = $document->confidentiality ?? 'internal';
        $userRole = $user->role ?? 'user';
        
        switch ($confidentiality) {
            case 'public':
                return true;
            case 'internal':
                return true;
            case 'restricted':
                return in_array($userRole, ['admin', 'super_admin', 'legal_admin', 'hr_admin']);
            default:
                return true;
        }
    }

    /**
     * Get user's access level for document
     */
    private function getAccessLevel($user, Document $document)
    {
        $confidentiality = $document->confidentiality ?? 'internal';
        $userRole = $user->role ?? 'user';
        
        if ($confidentiality === 'restricted' && in_array($userRole, ['admin', 'super_admin', 'legal_admin'])) {
            return 'full_access';
        } elseif ($confidentiality === 'internal') {
            return 'standard_access';
        } else {
            return 'limited_access';
        }
    }

    /**
     * Log document access
     */
    private function logDocumentAccess(Document $document, $user, $action)
    {
        // Log to DocumentAccessLog
        DocumentAccessLog::create([
            'document_id' => $document->id,
            'user_id' => $user->id,
            'action' => $action,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'accessed_at' => now()
        ]);

        // Log to general AccessLog
        AccessLog::create([
            'user_id' => $user->id,
            'action' => 'document_' . $action,
            'description' => "Document {$action}: {$document->title} (ID: {$document->id})",
            'ip_address' => request()->ip(),
            'metadata' => [
                'document_id' => $document->id,
                'document_title' => $document->title,
                'confidentiality' => $document->confidentiality,
                'user_role' => $user->role ?? 'unknown'
            ]
        ]);
    }

    /**
     * Generate access analytics for document
     */
    private function generateAccessAnalytics(Document $document)
    {
        $accessLogs = DocumentAccessLog::where('document_id', $document->id)
            ->orderBy('accessed_at', 'desc')
            ->get();

        $totalAccesses = $accessLogs->count();
        $uniqueUsers = $accessLogs->pluck('user_id')->unique()->count();
        $downloads = $accessLogs->where('action', 'download')->count();
        $views = $accessLogs->where('action', 'view')->count();

        $recentAccesses = $accessLogs->take(10)->map(function ($log) {
            return [
                'user_id' => $log->user_id,
                'action' => $log->action,
                'accessed_at' => $log->accessed_at,
                'ip_address' => $log->ip_address
            ];
        });

        return [
            'total_accesses' => $totalAccesses,
            'unique_users' => $uniqueUsers,
            'downloads' => $downloads,
            'views' => $views,
            'recent_accesses' => $recentAccesses,
            'confidentiality_level' => $document->confidentiality,
            'document_created' => $document->created_at,
            'last_accessed' => $accessLogs->first()?->accessed_at
        ];
    }
}
