<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->paginate(15);
        
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read.
     *
     * @param  string  $notificationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($notificationId);
        
        $notification->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => __('Notification marked as read.')
        ]);
    }

    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => __('All notifications marked as read.')
        ]);
    }

    /**
     * Delete a notification.
     *
     * @param  string  $notificationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($notificationId);
        
        $notification->delete();
        
        return response()->json([
            'success' => true,
            'message' => __('Notification deleted.')
        ]);
    }

    /**
     * Get unread notifications count.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function unreadCount()
    {
        $user = Auth::user();
        $count = $user->unreadNotifications()->count();
        
        return response()->json([
            'count' => $count
        ]);
    }
}