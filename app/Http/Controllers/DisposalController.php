<?php

namespace App\Http\Controllers;

use App\Models\DisposalHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisposalController extends Controller
{
    /**
     * Display disposal history (disposed documents)
     */
    public function index(Request $request)
    {
        // Simple query without filtering
        $disposedDocuments = DisposalHistory::orderBy('disposed_at', 'desc')->paginate(20);

        // Get statistics
        $stats = [
            'total_disposed' => DisposalHistory::count(),
            'auto_expired' => DisposalHistory::where('disposal_reason', 'auto_expired')->count(),
            'manually_disposed' => DisposalHistory::where('disposal_reason', 'manually_disposed')->count(),
            'this_month' => DisposalHistory::whereMonth('disposed_at', now()->month)
                ->whereYear('disposed_at', now()->year)
                ->count(),
            'this_week' => DisposalHistory::whereBetween('disposed_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count()
        ];

        return view('disposal.index', compact('disposedDocuments', 'stats'));
    }

    /**
     * Show details of a disposed document
     */
    public function show($id)
    {
        $disposedDocument = DisposalHistory::findOrFail($id);
        
        return view('disposal.show', compact('disposedDocument'));
    }

    /**
     * Export disposal history
     */
    public function export(Request $request)
    {
        // Simple query without filtering
        $disposedDocuments = DisposalHistory::orderBy('disposed_at', 'desc')->get();

        // Generate CSV
        $filename = 'disposal_history_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($disposedDocuments) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Document Title',
                'Description',
                'Category',
                'Department',
                'Author',
                'File Name',
                'File Type',
                'File Size',
                'Confidentiality Level',
                'Retention Until',
                'Retention Policy',
                'Previous Status',
                'Disposal Reason',
                'Disposed At',
                'Disposed By',
                'IP Address'
            ]);

            // CSV Data
            foreach ($disposedDocuments as $doc) {
                fputcsv($file, [
                    $doc->document_title,
                    $doc->document_description,
                    $doc->document_category,
                    $doc->document_department,
                    $doc->document_author,
                    $doc->file_name,
                    $doc->file_type,
                    $doc->formatted_file_size,
                    $doc->confidentiality_level,
                    $doc->retention_until?->format('Y-m-d'),
                    $doc->retention_policy,
                    $doc->previous_status,
                    $doc->disposal_reason_display,
                    $doc->disposed_at->format('Y-m-d H:i:s'),
                    $doc->disposer?->name ?? 'System',
                    $doc->ip_address
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}