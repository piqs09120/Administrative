<?php

namespace App\Http\Controllers;

use App\Models\{Document, DocumentVersion, DocumentActivityLog, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentApiController extends Controller
{
    public function show(Document $doc)
    {
        return response()->json([
            'success'  => true,
            'document' => [
                'id' => $doc->id,
                'title' => $doc->title,
                'description' => $doc->description,
                'type' => $doc->type,
                'category' => $doc->category,
                'department' => $doc->department,
                'confidentiality' => $doc->confidentiality_level ?? $doc->confidentiality,
                'status' => $doc->status,
                'created_at' => $doc->created_at,
                'updated_at' => $doc->updated_at,
                'retention_until' => $doc->retention_until,
            ],
        ]);
    }

    public function logView(Document $doc)
    {
        $doc->increment('view_count');
        $doc->log('viewed','Document viewed');
        return response()->noContent();
    }

    public function logDownload(Document $doc)
    {
        $doc->increment('download_count');
        $doc->log('downloaded','Document downloaded');
        return response()->noContent();
    }

    public function download(Document $doc)
    {
        // secure: adjust to your storage setup
        $disk = Storage::disk('public');
        if (!$disk->exists($doc->file_path)) {
            abort(404, 'File not found');
        }
        return $disk->download($doc->file_path, ($doc->title ?? 'document') . '.pdf');
    }

    /** Collaboration */
    public function listCollaborators(Document $doc)
    {
        return response()->json([
            'success' => true,
            'collaborators' => $doc->collaborators()
                ->get()
                ->map(fn($u)=>[
                    'user_id' => $u->id,
                    'user_name' => $u->name ?? 'Unknown',
                    'role' => $u->pivot->role,
                    'added_at' => $u->pivot->added_at,
                ]),
        ]);
    }

    public function addCollaborator(Request $r, Document $doc)
    {
        $data = $r->validate([
            'user_id' => ['required','exists:users,id','different:'.auth()->id()],
            'role'    => ['required','in:viewer,editor,reviewer,admin'],
        ]);
        $doc->collaborators()->syncWithoutDetaching([$data['user_id'] => ['role'=>$data['role']]]);
        $doc->log('collaborator_added','Collaborator added', $data);
        return response()->json(['success'=>true]);
    }

    public function removeCollaborator(Request $r, Document $doc, $userId)
    {
        $user = User::findOrFail($userId);
        $doc->collaborators()->detach($user->id);
        $doc->log('collaborator_removed','Collaborator removed', ['user_id'=>$user->id]);
        return response()->json(['success'=>true]);
    }

    /** History (edit versions + access log + small stats) */
    public function history(Document $doc)
    {
        return response()->json([
            'success' => true,
            'editing_history' => $doc->versions()
                ->take(25)
                ->get()
                ->map(fn($v)=>[
                    'action'     => 'Edited (v'.$v->version.')',
                    'description'=> optional($v->changes)['summary'] ?? null,
                    'user_name'  => optional($v->editor)->name ?? 'System',
                    'timestamp'  => $v->created_at,
                ]),
            'access_log' => $doc->activityLogs()
                ->latest()->take(40)->get()
                ->map(fn($a)=>[
                    'action'       => ucfirst($a->action),
                    'user_name'    => optional($a->user)->name ?? 'System',
                    'ip_address'   => $a->ip_address,
                    'timestamp'    => $a->created_at,
                ]),
            'stats' => [
                'view_count'         => $doc->view_count ?? 0,
                'download_count'     => $doc->download_count ?? 0,
                'version'            => optional($doc->versions()->first())->version ?? 1,
                'collaborators_count'=> $doc->collaborators()->count(),
            ],
        ]);
    }

    /** Activity tracking (filters + pagination) */
    public function activityTracking(Request $r, Document $doc)
    {
        $log = $doc->activityLogs()->with('user')->latest();
        if ($u = $r->string('user'))   $log->whereHas('user', fn($q)=>$q->where('name','like',"%{$u}%"));
        if ($a = $r->string('action')) $log->where('action',$a);
        if ($df = $r->date('date_from')) $log->whereDate('created_at','>=',$df);
        if ($dt = $r->date('date_to'))   $log->whereDate('created_at','<=',$dt);
        $per = (int) $r->input('per_page', 10);
        $page = (int) $r->input('page', 1);
        $p = $log->paginate($per, ['*'], 'page', $page);
        return response()->json([
            'success' => true,
            'activity_log' => $p->map(fn($x)=>[
                'user_name'     => optional($x->user)->name ?? 'System',
                'action'        => ucfirst($x->action),
                'description'   => $x->description,
                'formatted_date'=> $x->created_at->format('M d, Y h:i A'),
                'ip_address'    => $x->ip_address,
            ]),
            'pagination' => [
                'current_page' => $p->currentPage(),
                'last_page'    => $p->lastPage(),
                'total'        => $p->total(),
            ],
        ]);
    }

    /** Archive lifecycle */
    public function archive(Document $doc)
    {
        $doc->update(['status'=>'archived','archived_by'=>auth()->id(),'archived_at'=>now()]);
        $doc->log('archived','Document archived');
        return response()->json(['success'=>true]);
    }

    public function unarchive(Document $doc)
    {
        $doc->update(['status'=>'active']);
        $doc->log('restored','Document restored from archive');
        return response()->json(['success'=>true]);
    }

    public function dispose(Document $doc)
    {
        $doc->update(['status'=>'disposed']);
        $doc->log('disposed','Document disposed');
        return response()->json(['success'=>true]);
    }
}



