<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'slot_interval_minutes',
        'buffer_before_minutes',
        'buffer_after_minutes',
        'max_advance_days',
        'min_notice_minutes',
        'cancellation_limit_hours',
        'requires_confirmation',
        'is_public',
        'timezone',
        'preferences',
    ];

    protected $casts = [
        'requires_confirmation' => 'boolean',
        'is_public' => 'boolean',
        'preferences' => 'array',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
