<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatRoomMember extends Model
{
    protected $fillable = ['chat_room_id', 'employee_id', 'is_admin', 'last_read_at'];

    protected function casts(): array
    {
        return [
            'is_admin'     => 'boolean',
            'last_read_at' => 'datetime',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function room()
    {
        return $this->belongsTo(ChatRoom::class, 'chat_room_id');
    }
}
