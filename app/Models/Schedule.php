<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'employee_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_recurring',
        'effective_from',
        'effective_to',
        'metadata',
    ];

    protected $casts = [
        'is_recurring' => 'bool',
        'metadata' => 'array',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function breaks(): HasMany
    {
        return $this->hasMany(ScheduleBreak::class);
    }
}
