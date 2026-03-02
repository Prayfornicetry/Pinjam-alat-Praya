<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Mark single notification as read
     */
    public function markAsRead(Request $request)
    {
        $validated = $request->validate([
            'notification_id' => 'required|string',
        ]);

        // Get current clicked notifications from session
        $clickedNotifications = json_decode(session('clicked_notifications', '[]'), true) ?? [];
        
        // Add new notification ID
        if (!in_array($validated['notification_id'], $clickedNotifications)) {
            $clickedNotifications[] = $validated['notification_id'];
        }
        
        // Keep only last 50 notifications to prevent session bloat
        $clickedNotifications = array_slice($clickedNotifications, -50);
        
        // Save to session
        session(['clicked_notifications' => json_encode($clickedNotifications)]);
        
        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllRead(Request $request)
    {
        // Get all unread notification IDs for this user
        $approvedBorrowings = \App\Models\Borrowing::where('user_id', Auth::id())
            ->where('status', 'approved')
            ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(7))
            ->get();
        
        // Get current clicked notifications
        $clickedNotifications = json_decode(session('clicked_notifications', '[]'), true) ?? [];
        
        // Add all notification IDs
        foreach ($approvedBorrowings as $borrowing) {
            $notifId = 'approved_' . $borrowing->id;
            if (!in_array($notifId, $clickedNotifications)) {
                $clickedNotifications[] = $notifId;
            }
        }
        
        // Keep only last 50 notifications
        $clickedNotifications = array_slice($clickedNotifications, -50);
        
        // Save to session
        session(['clicked_notifications' => json_encode($clickedNotifications)]);
        
        return response()->json(['success' => true]);
    }

    /**
     * Clear all read notifications
     */
    public function clearAll()
    {
        session(['clicked_notifications' => json_encode([])]);
        
        return redirect()->back()->with('success', '✅ Semua notifikasi sudah dibersihkan!');
    }
}