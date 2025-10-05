<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LoginAttempt extends Model
{
    protected $fillable = [
        'identifier',
        'login_type',
        'ip_address',
        'attempts',
        'last_attempt_at',
        'locked_until'
    ];

    protected $casts = [
        'last_attempt_at' => 'datetime',
        'locked_until' => 'datetime'
    ];

    /**
     * Check if the account is currently locked
     */
    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Get remaining lockout time in seconds
     */
    public function getRemainingLockoutTime(): int
    {
        if (!$this->isLocked()) {
            return 0;
        }
        
        return $this->locked_until->diffInSeconds(now());
    }

    /**
     * Reset attempts count
     */
    public function resetAttempts(): void
    {
        $this->update([
            'attempts' => 0,
            'last_attempt_at' => null,
            'locked_until' => null
        ]);
    }

    /**
     * Increment failed attempts
     */
    public function incrementAttempts(): void
    {
        $this->increment('attempts');
        $this->update(['last_attempt_at' => now()]);
        
        // Lock account after 3 failed attempts for 3 minutes
        if ($this->attempts >= 3) {
            $this->update(['locked_until' => now()->addMinutes(3)]);
        }
    }

    /**
     * Scope to find by identifier and login type
     */
    public function scopeForLogin($query, string $identifier, string $loginType)
    {
        return $query->where('identifier', $identifier)
                    ->where('login_type', $loginType);
    }

    /**
     * Scope to find active (non-expired) records
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('locked_until')
              ->orWhere('locked_until', '>', now());
        });
    }
}
