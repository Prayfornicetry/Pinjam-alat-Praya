<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Get chat messages
     */
    public function getMessages(Request $request)
    {
        $user = Auth::user();
        
        if ($user->isAdmin() || $user->isStaff()) {
            // Admin melihat chat dengan user tertentu (jika ada user_id)
            $query = Chat::with(['user', 'admin']);
            
            // Jika ada user_id parameter, filter untuk user tersebut
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
            
            $messages = $query->orderBy('created_at', 'asc')
                ->take(50)
                ->get()
                ->map(function($msg) {
                    return [
                        'id' => $msg->id,
                        'message' => $msg->message,
                        'sender' => $msg->sender,
                        'created_at' => $msg->created_at,
                        'user_name' => $msg->user->name ?? 'User',
                        'admin_name' => $msg->admin->name ?? 'Admin',
                        'user_id' => $msg->user_id,
                        'is_read' => $msg->is_read,
                        'read_at' => $msg->read_at,
                    ];
                });
        } else {
            // User melihat chat mereka sendiri
            $messages = Chat::where('user_id', $user->id)
                ->with(['admin'])
                ->orderBy('created_at', 'asc')
                ->take(50)
                ->get()
                ->map(function($msg) use ($user) {
                    return [
                        'id' => $msg->id,
                        'message' => $msg->message,
                        'sender' => $msg->sender,
                        'created_at' => $msg->created_at,
                        'user_name' => $user->name,
                        'admin_name' => $msg->admin->name ?? 'Admin',
                        'is_read' => $msg->is_read,
                        'read_at' => $msg->read_at,
                    ];
                });
        }

        return response()->json(['messages' => $messages]);
    }

    /**
     * Store new message
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $user = Auth::user();
        $isAdmin = $user->isAdmin() || $user->isStaff();

        // Jika admin, user_id WAJIB ada
        if ($isAdmin && empty($validated['user_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'User ID harus diisi untuk admin'
            ], 422);
        }

        $chat = Chat::create([
            'user_id' => $isAdmin ? $validated['user_id'] : $user->id,
            'admin_id' => $isAdmin ? $user->id : null,
            'message' => $validated['message'],
            'sender' => $isAdmin ? 'admin' : 'user',
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $chat->id,
                'message' => $chat->message,
                'sender' => $chat->sender,
                'created_at' => $chat->created_at,
                'is_read' => $chat->is_read,
                'read_at' => $chat->read_at,
            ]
        ]);
    }

    /**
     * Get unread count
     */
    public function getUnreadCount()
    {
        $user = Auth::user();

        if ($user->isAdmin() || $user->isStaff()) {
            $count = Chat::where('sender', 'user')
                ->where('is_read', false)
                ->count();
        } else {
            $count = Chat::where('user_id', $user->id)
                ->where('sender', 'admin')
                ->where('is_read', false)
                ->count();
        }

        return response()->json(['count' => $count]);
    }

    /**
     * Get list of users who have chatted (for admin)
     */
    public function getUserList()
    {
        $user = Auth::user();
        
        if (!($user->isAdmin() || $user->isStaff())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $users = Chat::select('user_id')
            ->with(['user' => function($q) {
                $q->select('id', 'name', 'email');
            }])
            ->groupBy('user_id')
            ->get()
            ->map(function($chat) {
                $lastMessage = Chat::where('user_id', $chat->user_id)
                    ->latest()
                    ->first();
                
                $unreadCount = Chat::where('user_id', $chat->user_id)
                    ->where('sender', 'user')
                    ->where('is_read', false)
                    ->count();
                
                return [
                    'user_id' => $chat->user_id,
                    'name' => $chat->user->name ?? 'Unknown',
                    'email' => $chat->user->email ?? '',
                    'last_message' => $lastMessage ? $lastMessage->created_at->diffForHumans() : null,
                    'unread_count' => $unreadCount,
                ];
            });

        return response()->json(['users' => $users]);
    }

    /**
     * Mark messages as read
     */
    public function markAsRead(Request $request)
    {
        $user = Auth::user();

        if ($user->isAdmin() || $user->isStaff()) {
            // Admin mark all messages from specific user as read
            if ($request->filled('user_id')) {
                Chat::where('user_id', $request->user_id)
                    ->where('sender', 'user')
                    ->where('is_read', false)
                    ->update(['is_read' => true, 'read_at' => now()]);
            }
        } else {
            // User mark all messages from admin as read
            Chat::where('user_id', $user->id)
                ->where('sender', 'admin')
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);
        }

        return response()->json(['success' => true]);
    }
}