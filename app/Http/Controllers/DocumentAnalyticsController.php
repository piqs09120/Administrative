<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentAccessLog;
use App\Models\AccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DocumentAnalyticsController extends Controller
{
    /**
     * Get comprehensive document analytics
     */
    public function index(Request $request)
    {
        // Check if user has permission to view analytics
        if (!$this->canViewAnalytics(Auth::user())) {
            return redirect()->back()->with('error', 'Access Denied: You do not have permission to view analytics.');
        }

        $dateRange = $request->get('date_range', '30'); // Default to last 30 days
        $department = $request->get('department');
        $confidentiality = $request->get('confidentiality');

        $analytics = $this->generateAnalytics($dateRange, $department, $confidentiality);

        return view('document.analytics', compact('analytics', 'dateRange', 'department', 'confidentiality'));
    }

    /**
     * Get document access analytics for a specific document
     */
    public function documentAccess($id)
    {
        $document = Document::findOrFail($id);
        
        // Check if user can access this document
        if (!$this->canAccessDocument(Auth::user(), $document)) {
            return redirect()->back()->with('error', 'Access Denied: You do not have permission to view this document.');
        }

        $analytics = $this->generateDocumentAccessAnalytics($document);
        
        return response()->json([
            'success' => true,
            'analytics' => $analytics
        ]);
    }

    /**
     * Get department-wise document statistics
     */
    public function departmentStats()
    {
        if (!$this->canViewAnalytics(Auth::user())) {
            return response()->json(['error' => 'Access Denied'], 403);
        }

        $stats = Document::select('department', DB::raw('count(*) as total_documents'))
            ->whereNotNull('department')
            ->groupBy('department')
            ->orderBy('total_documents', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'department_stats' => $stats
        ]);
    }

    /**
     * Get confidentiality level statistics
     */
    public function confidentialityStats()
    {
        if (!$this->canViewAnalytics(Auth::user())) {
            return response()->json(['error' => 'Access Denied'], 403);
        }

        $stats = Document::select('confidentiality', DB::raw('count(*) as total_documents'))
            ->whereNotNull('confidentiality')
            ->groupBy('confidentiality')
            ->orderBy('total_documents', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'confidentiality_stats' => $stats
        ]);
    }

    /**
     * Get document access trends
     */
    public function accessTrends(Request $request)
    {
        if (!$this->canViewAnalytics(Auth::user())) {
            return response()->json(['error' => 'Access Denied'], 403);
        }

        $days = $request->get('days', 30);
        $startDate = Carbon::now()->subDays($days);
        
        $trends = DocumentAccessLog::select(
                DB::raw('DATE(accessed_at) as date'),
                DB::raw('count(*) as total_accesses'),
                DB::raw('count(DISTINCT user_id) as unique_users'),
                DB::raw('sum(case when action = "download" then 1 else 0 end) as downloads'),
                DB::raw('sum(case when action = "view" then 1 else 0 end) as views')
            )
            ->where('accessed_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'success' => true,
            'trends' => $trends
        ]);
    }

    /**
     * Generate comprehensive analytics
     */
    private function generateAnalytics($dateRange, $department = null, $confidentiality = null)
    {
        $startDate = Carbon::now()->subDays($dateRange);
        
        // Base query
        $query = Document::query();
        
        if ($department) {
            $query->where('department', $department);
        }
        
        if ($confidentiality) {
            $query->where('confidentiality', $confidentiality);
        }

        // Document statistics
        $totalDocuments = $query->count();
        $activeDocuments = $query->where('status', 'active')->count();
        $archivedDocuments = $query->where('status', 'archived')->count();
        $expiredDocuments = $query->where('status', 'expired')->count();
        $disposedDocuments = $query->where('status', 'disposed')->count();

        // Access statistics
        $accessQuery = DocumentAccessLog::where('accessed_at', '>=', $startDate);
        if ($department) {
            $accessQuery->whereHas('document', function($q) use ($department) {
                $q->where('department', $department);
            });
        }
        if ($confidentiality) {
            $accessQuery->whereHas('document', function($q) use ($confidentiality) {
                $q->where('confidentiality', $confidentiality);
            });
        }

        $totalAccesses = $accessQuery->count();
        $uniqueUsers = $accessQuery->distinct('user_id')->count();
        $downloads = $accessQuery->where('action', 'download')->count();
        $views = $accessQuery->where('action', 'view')->count();

        // Department breakdown
        $departmentBreakdown = Document::select('department', DB::raw('count(*) as count'))
            ->whereNotNull('department')
            ->groupBy('department')
            ->orderBy('count', 'desc')
            ->get();

        // Confidentiality breakdown
        $confidentialityBreakdown = Document::select('confidentiality', DB::raw('count(*) as count'))
            ->whereNotNull('confidentiality')
            ->groupBy('confidentiality')
            ->orderBy('count', 'desc')
            ->get();

        // Recent activity
        $recentActivity = DocumentAccessLog::with('document')
            ->where('accessed_at', '>=', $startDate)
            ->orderBy('accessed_at', 'desc')
            ->limit(10)
            ->get();

        // Expiring documents
        $expiringDocuments = Document::whereNotNull('retention_until')
            ->where('retention_until', '<=', Carbon::now()->addDays(30))
            ->where('retention_until', '>', Carbon::now())
            ->where('status', '!=', 'disposed')
            ->orderBy('retention_until')
            ->get();

        return [
            'overview' => [
                'total_documents' => $totalDocuments,
                'active_documents' => $activeDocuments,
                'archived_documents' => $archivedDocuments,
                'expired_documents' => $expiredDocuments,
                'disposed_documents' => $disposedDocuments,
            ],
            'access_stats' => [
                'total_accesses' => $totalAccesses,
                'unique_users' => $uniqueUsers,
                'downloads' => $downloads,
                'views' => $views,
                'avg_accesses_per_document' => $totalDocuments > 0 ? round($totalAccesses / $totalDocuments, 2) : 0,
            ],
            'department_breakdown' => $departmentBreakdown,
            'confidentiality_breakdown' => $confidentialityBreakdown,
            'recent_activity' => $recentActivity,
            'expiring_documents' => $expiringDocuments,
            'date_range' => $dateRange,
            'generated_at' => now()->toISOString()
        ];
    }

    /**
     * Generate document-specific access analytics
     */
    private function generateDocumentAccessAnalytics(Document $document)
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

        $monthlyTrends = DocumentAccessLog::select(
                DB::raw('YEAR(accessed_at) as year'),
                DB::raw('MONTH(accessed_at) as month'),
                DB::raw('count(*) as accesses')
            )
            ->where('document_id', $document->id)
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return [
            'document_info' => [
                'id' => $document->id,
                'title' => $document->title,
                'document_uid' => $document->document_uid,
                'department' => $document->department,
                'confidentiality' => $document->confidentiality,
                'status' => $document->status,
                'created_at' => $document->created_at,
            ],
            'access_stats' => [
                'total_accesses' => $totalAccesses,
                'unique_users' => $uniqueUsers,
                'downloads' => $downloads,
                'views' => $views,
                'last_accessed' => $accessLogs->first()?->accessed_at,
            ],
            'recent_accesses' => $recentAccesses,
            'monthly_trends' => $monthlyTrends,
        ];
    }

    /**
     * Check if user can view analytics
     */
    private function canViewAnalytics($user)
    {
        $userRole = $user->role ?? 'user';
        return in_array($userRole, ['admin', 'super_admin', 'legal_admin', 'hr_admin']);
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
}
