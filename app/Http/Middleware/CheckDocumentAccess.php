<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\AccessLog;
use Illuminate\Support\Facades\Auth;

class CheckDocumentAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Get document ID from route parameters
        $documentId = $request->route('id') ?? $request->route('document');
        
        if (!$documentId) {
            return $next($request);
        }

        // Get the document
        $document = Document::find($documentId);
        
        if (!$document) {
            return response()->json(['error' => 'Document not found'], 404);
        }

        // Check if user is authenticated
        if (!Auth::check()) {
            $this->logAccessAttempt($document, 'unauthenticated_access_attempt');
            return redirect()->route('login')->with('error', 'Authentication required to access documents.');
        }

        $user = Auth::user();
        
        // Check access based on confidentiality level
        if (!$this->canAccessDocument($user, $document)) {
            $this->logAccessAttempt($document, 'unauthorized_access_attempt', [
                'confidentiality' => $document->confidentiality,
                'user_role' => $user->role ?? 'unknown'
            ]);
            
            return response()->json([
                'error' => 'Access Denied',
                'message' => 'You do not have permission to access this document.',
                'confidentiality_level' => $document->confidentiality
            ], 403);
        }

        // Log successful access
        $this->logAccessAttempt($document, 'successful_access', [
            'confidentiality' => $document->confidentiality,
            'user_role' => $user->role ?? 'unknown'
        ]);

        return $next($request);
    }

    /**
     * Check if user can access document based on confidentiality level
     */
    private function canAccessDocument($user, Document $document)
    {
        $confidentiality = $document->confidentiality ?? 'internal';
        $userRole = $user->role ?? 'user';
        
        switch ($confidentiality) {
            case 'public':
                // Everyone can access public documents
                return true;
                
            case 'internal':
                // Internal documents - authenticated users only
                return true;
                
            case 'restricted':
                // Restricted documents - only administrators and specific roles
                return in_array($userRole, ['admin', 'super_admin', 'legal_admin', 'hr_admin']);
                
            default:
                // Default to internal access
                return true;
        }
    }

    /**
     * Log access attempts for audit trail
     */
    private function logAccessAttempt(Document $document, $action, $details = [])
    {
        AccessLog::create([
            'user_id' => Auth::id() ?? 'system',
            'action' => $action,
            'description' => "Document access attempt: {$action} for document ID {$document->id}",
            'ip_address' => request()->ip(),
            'metadata' => array_merge($details, [
                'document_id' => $document->id,
                'document_title' => $document->title,
                'confidentiality' => $document->confidentiality
            ])
        ]);
    }
}
