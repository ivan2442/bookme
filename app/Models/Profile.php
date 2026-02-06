<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Profile extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'owner_id',
        'name',
        'slug',
        'category',
        'description',
        'is_multilingual',
        'email',
        'phone',
        'website',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'timezone',
        'status',
        'subscription_starts_at',
        'subscription_plan',
        'logo_path',
        'banner_path',
        'settings',
        'billing_name',
        'billing_address',
        'billing_city',
        'billing_postal_code',
        'billing_country',
        'billing_ico',
        'billing_dic',
        'billing_ic_dph',
        'billing_iban',
        'billing_swift',
    ];

    protected $appends = [
        'logo_url',
        'banner_url',
        'trial_days_left',
        'trial_time_left',
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
        'settings' => 'array',
        'subscription_starts_at' => 'datetime',
        'is_multilingual' => 'boolean',
    ];

    public function getLogoUrlAttribute()
    {
        return $this->logo_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->logo_path) : null;
    }

    public function getBannerUrlAttribute()
    {
        return $this->banner_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->banner_path) : null;
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function holidays(): HasMany
    {
        return $this->hasMany(Holiday::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function calendarSetting(): HasOne
    {
        return $this->hasOne(CalendarSetting::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function getTrialEndsAtAttribute()
    {
        if (!$this->subscription_starts_at) {
            return null;
        }
        return $this->subscription_starts_at->copy()->addMonths(3);
    }

    public function getIsTrialActiveAttribute()
    {
        $trialEndsAt = $this->trial_ends_at;
        if (!$trialEndsAt) {
            return false;
        }
        return now()->lessThan($trialEndsAt);
    }

    public function getTrialDaysLeftAttribute()
    {
        $trialEndsAt = $this->trial_ends_at;
        if (!$trialEndsAt) {
            return 0;
        }
        if (now()->greaterThanOrEqualTo($trialEndsAt)) {
            return 0;
        }
        return (int) ceil(now()->diffInMinutes($trialEndsAt) / (24 * 60));
    }

    public function getTrialTimeLeftAttribute()
    {
        $trialEndsAt = $this->trial_ends_at;
        if (!$trialEndsAt || now()->greaterThanOrEqualTo($trialEndsAt)) {
            return '0 dní';
        }

        $diff = now()->diff($trialEndsAt);
        $parts = [];

        if ($diff->m > 0) {
            $monthWord = $diff->m == 1 ? 'Mesiac' : ($diff->m < 5 ? 'Mesiace' : 'Mesiacov');
            $parts[] = $diff->m . ' ' . $monthWord;
        }

        if ($diff->d > 0) {
            $dayWord = $diff->d == 1 ? 'Deň' : ($diff->d < 5 ? 'Dni' : 'Dní');
            $parts[] = $diff->d . ' ' . $dayWord;
        }

        if (empty($parts)) {
            return 'Menej ako deň';
        }

        return implode(' ', $parts);
    }
}
