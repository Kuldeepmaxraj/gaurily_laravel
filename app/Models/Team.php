<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['name', 'description', 'is_active'];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
