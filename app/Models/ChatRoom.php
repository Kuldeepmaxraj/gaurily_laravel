<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    protected $fillable = ['name', 'type', 'created_by_id', 'team_id'];

    public function members()
    {
        return $this->hasMany(ChatRoomMember::class);
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Employee::class, 'created_by_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function getDisplayName(Employee $forEmployee): string
    {
        if ($this->type === 'direct') {
            $other = $this->members->firstWhere('employee_id', '!=', $forEmployee->id);
            return $other?->employee?->name ?? 'Direct Message';
        }
        return $this->name ?? 'Group Chat';
    }
}
