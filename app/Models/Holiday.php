<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = ['holiday_date', 'name', 'is_optional'];

    protected function casts(): array
    {
        return [
            'holiday_date' => 'date',
            'is_optional'  => 'boolean',
        ];
    }

    public static function isHoliday(\Carbon\Carbon $date): bool
    {
        return static::where('holiday_date', $date->toDateString())->exists();
    }
}
