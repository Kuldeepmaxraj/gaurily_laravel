<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\ChatRoomMember;
use App\Models\ChatMessage;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    private function me(): Employee
    {
        return auth()->user();
    }

    // ─── Pages ────────────────────────────────────────────────────────────────

    public function index()
    {
        $employee  = $this->me();
        $employee->update(['last_seen_at' => now()]);
        $employees = Employee::where('status', 'active')
            ->where('id', '!=', $employee->id)
            ->with('role', 'team')
            ->orderBy('name')
            ->get();

        return view('chat.index', compact('employee', 'employees'));
    }

    // ─── API ──────────────────────────────────────────────────────────────────

    /** List rooms the current user belongs to (called every ~8s for unread counts). */
    public function rooms()
    {
        $employee = $this->me();

        $memberRows = ChatRoomMember::where('employee_id', $employee->id)
            ->with(['room.members.employee'])
            ->get();

        $rooms = $memberRows->map(function ($member) use ($employee) {
            $room = $member->room;
            if (!$room) return null;

            $lastMsg = ChatMessage::where('chat_room_id', $room->id)
                ->with('employee')
                ->latest()
                ->first();

            $unread = ChatMessage::where('chat_room_id', $room->id)
                ->where('employee_id', '!=', $employee->id)
                ->when($member->last_read_at, fn ($q) => $q->where('created_at', '>', $member->last_read_at))
                ->count();

            $name   = $this->displayName($room, $employee);
            $avatar = null;
            if ($room->type === 'direct') {
                $other  = $room->members->firstWhere('employee_id', '!=', $employee->id);
                $avatar = $other?->employee?->avatar ? Storage::url($other->employee->avatar) : null;
            }

            return [
                'id'           => $room->id,
                'name'         => $name,
                'type'         => $room->type,
                'is_admin'     => (bool) $member->is_admin,
                'unread'       => $unread,
                'avatar'       => $avatar,
                'members'      => $room->members->map(fn ($m) => [
                    'id'     => $m->employee_id,
                    'name'   => $m->employee?->name,
                    'online' => $m->employee?->isOnline(),
                ]),
                'last_message' => $lastMsg ? [
                    'body'  => Str::limit($lastMsg->body, 50),
                    'mine'  => $lastMsg->employee_id === $employee->id,
                    'time'  => $lastMsg->created_at->diffForHumans(null, true),
                ] : null,
                'updated_at'   => $room->updated_at?->timestamp ?? 0,
            ];
        })
        ->filter()
        ->sortByDesc('updated_at')
        ->values();

        return response()->json($rooms);
    }

    /** Messages in a room; ?after=id returns only newer messages. */
    public function messages(ChatRoom $room, Request $request)
    {
        $employee = $this->me();
        $this->requireMember($room, $employee);

        $after = (int) $request->input('after', 0);

        if ($after > 0) {
            $msgs = ChatMessage::where('chat_room_id', $room->id)
                ->where('id', '>', $after)
                ->with('employee')
                ->orderBy('id')
                ->get();
        } else {
            $ids  = ChatMessage::where('chat_room_id', $room->id)->latest()->limit(60)->pluck('id');
            $msgs = ChatMessage::whereIn('id', $ids)->with('employee')->orderBy('id')->get();
        }

        $formatted = $msgs->map(fn ($m) => $this->formatMessage($m, $employee));

        return response()->json([
            'messages' => $formatted,
            'last_id'  => $formatted->max('id') ?? $after,
        ]);
    }

    /** Send a text message to a room. */
    public function send(Request $request, ChatRoom $room)
    {
        $employee = $this->me();
        $this->requireMember($room, $employee);

        $data = $request->validate(['body' => 'required|string|max:4000']);

        $message = ChatMessage::create([
            'chat_room_id' => $room->id,
            'employee_id'  => $employee->id,
            'body'         => $data['body'],
        ]);

        $this->afterSend($room, $employee);
        $message->load('employee');

        return response()->json($this->formatMessage($message, $employee), 201);
    }

    /** Upload a file/image attachment and create a chat message. */
    public function sendFile(Request $request, ChatRoom $room)
    {
        $employee = $this->me();
        $this->requireMember($room, $employee);

        $request->validate([
            'file' => 'required|file|max:20480', // 20 MB
            'body' => 'nullable|string|max:500',
        ]);

        $file  = $request->file('file');
        $mime  = $file->getMimeType();
        $origName = $file->getClientOriginalName();
        $path  = $file->store('chat-attachments', 'public');

        $message = ChatMessage::create([
            'chat_room_id'    => $room->id,
            'employee_id'     => $employee->id,
            'body'            => $request->input('body', ''),
            'attachment_path' => $path,
            'attachment_name' => $origName,
            'attachment_mime' => $mime,
            'attachment_size' => $file->getSize(),
        ]);

        $this->afterSend($room, $employee);
        $message->load('employee');

        return response()->json($this->formatMessage($message, $employee), 201);
    }

    private function afterSend(ChatRoom $room, Employee $employee): void
    {
        ChatRoomMember::where('chat_room_id', $room->id)
            ->where('employee_id', $employee->id)
            ->update(['last_read_at' => now()]);
        $room->touch();
    }

    /** Find or create a direct DM room between the current user and another. */
    public function createDirect(Request $request)
    {
        $employee = $this->me();
        $data     = $request->validate(['employee_id' => 'required|exists:employees,id']);
        $otherId  = (int) $data['employee_id'];

        if ($otherId === $employee->id) {
            return response()->json(['error' => 'Cannot DM yourself.'], 422);
        }

        // Find existing direct room shared by both
        $myRoomIds = ChatRoomMember::where('employee_id', $employee->id)
            ->whereHas('room', fn ($q) => $q->where('type', 'direct'))
            ->pluck('chat_room_id');

        $existing = ChatRoomMember::where('employee_id', $otherId)
            ->whereIn('chat_room_id', $myRoomIds)
            ->first();

        if ($existing) {
            return response()->json(['id' => $existing->chat_room_id]);
        }

        $room = ChatRoom::create(['type' => 'direct', 'created_by_id' => $employee->id]);
        ChatRoomMember::create(['chat_room_id' => $room->id, 'employee_id' => $employee->id, 'is_admin' => true]);
        ChatRoomMember::create(['chat_room_id' => $room->id, 'employee_id' => $otherId]);

        return response()->json(['id' => $room->id], 201);
    }

    /** Create a named group chat (admin/hr/team_lead only). */
    public function createGroup(Request $request)
    {
        $employee = $this->me();
        if (!in_array($employee->role?->name, ['admin', 'hr', 'team_lead'])) {
            return response()->json(['error' => 'Only admins, HR and team leads can create group chats.'], 403);
        }

        $data = $request->validate([
            'name'           => 'required|string|max:80',
            'team_id'        => 'nullable|exists:teams,id',
            'member_ids'     => 'nullable|array',
            'member_ids.*'   => 'exists:employees,id',
        ]);

        $room = ChatRoom::create([
            'name'          => $data['name'],
            'type'          => 'group',
            'created_by_id' => $employee->id,
            'team_id'       => $data['team_id'] ?? null,
        ]);

        ChatRoomMember::create(['chat_room_id' => $room->id, 'employee_id' => $employee->id, 'is_admin' => true]);

        foreach (collect($data['member_ids'] ?? [])->unique()->reject(fn ($id) => $id == $employee->id) as $memberId) {
            ChatRoomMember::create(['chat_room_id' => $room->id, 'employee_id' => $memberId]);
        }

        return response()->json(['id' => $room->id], 201);
    }

    /** Add a member to a group room (room admin or global admin). */
    public function addMember(Request $request, ChatRoom $room)
    {
        $employee = $this->me();
        $this->requireRoomAdmin($room, $employee);

        $data = $request->validate(['employee_id' => 'required|exists:employees,id']);

        ChatRoomMember::firstOrCreate([
            'chat_room_id' => $room->id,
            'employee_id'  => (int) $data['employee_id'],
        ], ['is_admin' => false]);

        return response()->json(['ok' => true]);
    }

    /** Remove a member (self-leave, room admin, or global admin). */
    public function removeMember(ChatRoom $room, Employee $target)
    {
        $employee = $this->me();
        if ($target->id !== $employee->id) {
            $this->requireRoomAdmin($room, $employee);
        }

        ChatRoomMember::where('chat_room_id', $room->id)
            ->where('employee_id', $target->id)
            ->delete();

        return response()->json(['ok' => true]);
    }

    /** Mark all messages in a room as read for the current user. */
    public function markRead(ChatRoom $room)
    {
        $employee = $this->me();
        ChatRoomMember::where('chat_room_id', $room->id)
            ->where('employee_id', $employee->id)
            ->update(['last_read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    /** Keep-alive ping to update online status. */
    public function ping()
    {
        $this->me()->update(['last_seen_at' => now()]);
        return response()->json(['ok' => true]);
    }

    /** List employees seen in the last 5 minutes. */
    public function online()
    {
        $employee  = $this->me();
        $threshold = now()->subMinutes(5);

        $online = Employee::where('status', 'active')
            ->where('last_seen_at', '>=', $threshold)
            ->where('id', '!=', $employee->id)
            ->orderBy('name')
            ->get()
            ->map(fn ($e) => [
                'id'          => $e->id,
                'name'        => $e->name,
                'designation' => $e->designation,
                'avatar'      => $e->avatar ? Storage::url($e->avatar) : null,
            ]);

        return response()->json($online);
    }

    /** Return total unread count for sidebar badge. */
    public function unreadTotal()
    {
        $employee   = $this->me();
        $memberRows = ChatRoomMember::where('employee_id', $employee->id)->get();

        $total = $memberRows->sum(fn ($m) =>
            ChatMessage::where('chat_room_id', $m->chat_room_id)
                ->where('employee_id', '!=', $employee->id)
                ->when($m->last_read_at, fn ($q) => $q->where('created_at', '>', $m->last_read_at))
                ->count()
        );

        return response()->json(['total' => $total]);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function requireMember(ChatRoom $room, Employee $employee): void
    {
        if (!ChatRoomMember::where('chat_room_id', $room->id)->where('employee_id', $employee->id)->exists()) {
            abort(403, 'You are not a member of this room.');
        }
    }

    private function requireRoomAdmin(ChatRoom $room, Employee $employee): void
    {
        $isRoomAdmin = ChatRoomMember::where('chat_room_id', $room->id)
            ->where('employee_id', $employee->id)
            ->where('is_admin', true)
            ->exists();

        if (!$isRoomAdmin && !$employee->isAdmin()) {
            abort(403, 'Only room admins can perform this action.');
        }
    }

    private function displayName(ChatRoom $room, Employee $employee): string
    {
        if ($room->type === 'direct') {
            $other = $room->members->firstWhere('employee_id', '!=', $employee->id);
            return $other?->employee?->name ?? 'Direct Message';
        }
        return $room->name ?? 'Group Chat';
    }

    private function formatMessage(ChatMessage $m, Employee $employee): array
    {
        $attachment = null;
        if ($m->attachment_path) {
            $attachment = [
                'url'   => Storage::url($m->attachment_path),
                'name'  => $m->attachment_name,
                'mime'  => $m->attachment_mime,
                'size'  => $m->attachment_size,
                'is_image' => str_starts_with($m->attachment_mime ?? '', 'image/'),
            ];
        }

        return [
            'id'         => $m->id,
            'body'       => $m->body,
            'mine'       => $m->employee_id === $employee->id,
            'sender'     => $m->employee?->name,
            'initials'   => strtoupper(substr($m->employee?->name ?? 'U', 0, 1)),
            'avatar'     => $m->employee?->avatar ? Storage::url($m->employee->avatar) : null,
            'time'       => $m->created_at->format('h:i A'),
            'date'       => $m->created_at->format('d M Y'),
            'attachment' => $attachment,
        ];
    }
}
