<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->latest()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function getUnreadCount()
    {
        $count = Auth::user()->unreadNotifications()->count();
        return response()->json(['count' => $count]);
    }

    public function list()
    {
        $notifications = Auth::user()->unreadNotifications()->latest()->take(10)->get();
        
        return response()->json([
            'count' => Auth::user()->unreadNotifications()->count(),
            'notifications' => $notifications
        ]);
    }

    public function markAsRead($id)
    {
        try {
            $notification = Auth::user()->notifications()->findOrFail($id);
            $notification->markAsRead();
            
            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Notification marked as read']);
            }
            
            return back();
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error marking notification as read'], 500);
            }
            return back()->with('error', 'Error marking notification as read');
        }
    }

    public function markAllAsRead()
    {
        try {
            $user = Auth::user();
            $user->unreadNotifications->markAsRead();
            
            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
            }
            
            return back();
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error marking all notifications as read'], 500);
            }
            return back()->with('error', 'Error marking all notifications as read');
        }
    }
} 