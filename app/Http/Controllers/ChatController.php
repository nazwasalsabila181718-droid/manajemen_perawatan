<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'driver') {
            $manager = User::where('role', 'manager')->first();

            if (!$manager) {
                return view('chat.index', [
                    'noManager' => true,
                    'messages' => collect(),
                    'otherUser' => null,
                ]);
            }

            $messages = ChatMessage::where(function ($q) use ($user, $manager) {
                $q->where('sender_id', $user->id)->where('receiver_id', $manager->id);
            })->orWhere(function ($q) use ($user, $manager) {
                $q->where('sender_id', $manager->id)->where('receiver_id', $user->id);
            })->orderBy('created_at')->get();

            ChatMessage::where('sender_id', $manager->id)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);

            return view('chat.index', [
                'otherUser' => $manager,
                'messages' => $messages,
                'drivers' => null,
            ]);
        }

        if ($user->role === 'manager') {
            $drivers = User::where('role', 'driver')->get()->map(function ($driver) use ($user) {
                $lastMessage = ChatMessage::where(function ($q) use ($user, $driver) {
                    $q->where('sender_id', $user->id)->where('receiver_id', $driver->id);
                })->orWhere(function ($q) use ($user, $driver) {
                    $q->where('sender_id', $driver->id)->where('receiver_id', $user->id);
                })->latest()->first();

                $unreadCount = ChatMessage::where('sender_id', $driver->id)
                    ->where('receiver_id', $user->id)
                    ->where('is_read', false)
                    ->count();

                $driver->last_message = $lastMessage;
                $driver->unread_count = $unreadCount;

                return $driver;
            })->sortByDesc(function ($driver) {
                return optional($driver->last_message)->created_at;
            })->values();

            $selectedDriver = null;
            $messages = collect();

            if ($request->filled('driver_id')) {
                $selectedDriver = User::where('role', 'driver')->find($request->driver_id);

                if ($selectedDriver) {
                    $messages = ChatMessage::where(function ($q) use ($user, $selectedDriver) {
                        $q->where('sender_id', $user->id)->where('receiver_id', $selectedDriver->id);
                    })->orWhere(function ($q) use ($user, $selectedDriver) {
                        $q->where('sender_id', $selectedDriver->id)->where('receiver_id', $user->id);
                    })->orderBy('created_at')->get();

                    ChatMessage::where('sender_id', $selectedDriver->id)
                        ->where('receiver_id', $user->id)
                        ->where('is_read', false)
                        ->update(['is_read' => true]);
                }
            }

            return view('chat.index', [
                'drivers' => $drivers,
                'otherUser' => $selectedDriver,
                'messages' => $messages,
            ]);
        }

        abort(403, 'Fitur chat hanya untuk driver dan manager.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:2000',
        ]);

        $chatMessage = ChatMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $chatMessage->id,
                'sender_id' => $chatMessage->sender_id,
                'message' => $chatMessage->message,
                'created_at' => $chatMessage->created_at->format('H:i'),
            ],
        ]);
    }

    public function poll(Request $request)
    {
        $user = Auth::user();
        $otherId = $request->query('with');
        $afterId = (int) $request->query('after_id', 0);

        if (!$otherId) {
            return response()->json(['messages' => []]);
        }

        $messages = ChatMessage::where(function ($q) use ($user, $otherId) {
            $q->where('sender_id', $user->id)->where('receiver_id', $otherId);
        })->orWhere(function ($q) use ($user, $otherId) {
            $q->where('sender_id', $otherId)->where('receiver_id', $user->id);
        })->where('id', '>', $afterId)
          ->orderBy('created_at')
          ->get();

        ChatMessage::where('sender_id', $otherId)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'messages' => $messages->map(function ($m) use ($user) {
                return [
                    'id' => $m->id,
                    'sender_id' => $m->sender_id,
                    'is_mine' => $m->sender_id === $user->id,
                    'message' => $m->message,
                    'created_at' => $m->created_at->format('H:i'),
                ];
            }),
        ]);
    }

    public function unreadCount(Request $request)
    {
        $user = Auth::user();

        $count = ChatMessage::where('receiver_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}