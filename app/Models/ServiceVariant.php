<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceVariant extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'service_id',
        'name',
        'description',
        'duration_minutes',
        'price',
        'currency',
        'buffer_before_minutes',
        'buffer_after_minutes',
        'is_active',
        'is_special',
    ];

    protected $translatable = [
        'name',
        'description',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'is_active' => 'bool',
        'is_special' => 'bool',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_service_variant')->withTimestamps();
    }

    public function availabilityRules(): HasMany
    {
        return $this->hasMany(ServiceAvailabilityRule::class, 'service_variant_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
