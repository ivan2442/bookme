<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'profile_id',
        'name',
        'category',
        'description',
        'base_duration_minutes',
        'base_price',
        'currency',
        'is_active',
        'is_special',
        'is_pakavoz_enabled',
        'pakavoz_api_key',
        'available_from',
        'available_to',
        'slot_interval_minutes',
    ];

    protected $translatable = [
        'name',
        'category',
        'description',
    ];

    protected $casts = [
        'name' => 'array',
        'category' => 'array',
        'description' => 'array',
        'is_active' => 'bool',
        'is_special' => 'bool',
        'is_pakavoz_enabled' => 'bool',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ServiceVariant::class);
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_service')->withTimestamps();
    }

    public function availabilityRules(): HasMany
    {
        return $this->hasMany(ServiceAvailabilityRule::class);
    }
}
