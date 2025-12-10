<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->recent(50)
            ->get();

        $unreadCount = Auth::user()->notifications()->unread()->count();

        if (request()->ajax()) {
            return response()->json([
                'notifications' => $notifications->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'link' => $notification->link,
                        'read' => $notification->read,
                        'created_at' => $notification->created_at->diffForHumans(),
                        'created_at_full' => $notification->created_at->format('M d, Y h:i A'),
                    ];
                }),
                'unread_count' => $unreadCount,
            ]);
        }

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $notification->markAsRead();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'unread_count' => Auth::user()->notifications()->unread()->count(),
            ]);
        }

        return redirect()->back();
    }

    public function markAllAsRead()
    {
        Auth::user()->notifications()->unread()->update(['read' => true]);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
            ]);
        }

        return redirect()->back();
    }

    public function destroy(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $notification->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'unread_count' => Auth::user()->notifications()->unread()->count(),
            ]);
        }

        return redirect()->back();
    }
}
