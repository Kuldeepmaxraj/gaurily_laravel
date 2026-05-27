<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = ['chat_room_id', 'employee_id', 'body', 'attachment_path', 'attachment_name', 'attachment_mime', 'attachment_size'];

    public function isImage(): bool
    {
        return str_starts_with($this->attachment_mime ?? '', 'image/');
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
