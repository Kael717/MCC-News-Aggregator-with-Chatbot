<?php

namespace App\Services;

use App\Models\LoginAttempt;
use Illuminate\Http\Request;

class LoginThrottleService
{
    /**
     * Check if login is allowed for the given identifier and login type
     */
    public function isLoginAllowed(string $identifier, string $loginType, Request $request): array
    {
        $loginAttempt = $this->getLoginAttempt($identifier, $loginType, $request);
        
        if (!$loginAttempt) {
            return [
                'allowed' => true,
                'attempts' => 0,
                'remaining_time' => 0,
                'message' => null
            ];
        }

        // Check if account is locked
        if ($loginAttempt->isLocked()) {
            $remainingTime = $loginAttempt->getRemainingLockoutTime();
            return [
                'allowed' => false,
                'attempts' => $loginAttempt->attempts,
                'remaining_time' => $remainingTime,
                'message' => "Account temporarily locked. Please try again in " . $this->formatTime($remainingTime) . "."
            ];
        }

        // Check if approaching limit
        if ($loginAttempt->attempts >= 2) {
            return [
                'allowed' => true,
                'attempts' => $loginAttempt->attempts,
                'remaining_time' => 0,
                'message' => "Warning: You have " . (3 - $loginAttempt->attempts) . " login attempt(s) remaining before your account is temporarily locked."
            ];
        }

        return [
            'allowed' => true,
            'attempts' => $loginAttempt->attempts,
            'remaining_time' => 0,
            'message' => $loginAttempt->attempts > 0 ? "Previous login attempt failed. " . (3 - $loginAttempt->attempts) . " attempts remaining." : null
        ];
    }

    /**
     * Record a failed login attempt
     */
    public function recordFailedAttempt(string $identifier, string $loginType, Request $request): array
    {
        $loginAttempt = $this->getOrCreateLoginAttempt($identifier, $loginType, $request);
        $loginAttempt->incrementAttempts();

        if ($loginAttempt->isLocked()) {
            return [
                'locked' => true,
                'attempts' => $loginAttempt->attempts,
                'remaining_time' => $loginAttempt->getRemainingLockoutTime(),
                'message' => "Too many failed attempts. Account locked for 3 minutes."
            ];
        }

        $remainingAttempts = 3 - $loginAttempt->attempts;
        return [
            'locked' => false,
            'attempts' => $loginAttempt->attempts,
            'remaining_time' => 0,
            'message' => "Invalid credentials. You have $remainingAttempts attempt(s) remaining."
        ];
    }

    /**
     * Record a successful login (reset attempts)
     */
    public function recordSuccessfulLogin(string $identifier, string $loginType, Request $request): void
    {
        $loginAttempt = $this->getLoginAttempt($identifier, $loginType, $request);
        
        if ($loginAttempt) {
            $loginAttempt->resetAttempts();
        }
    }

    /**
     * Get existing login attempt record
     */
    private function getLoginAttempt(string $identifier, string $loginType, Request $request): ?LoginAttempt
    {
        return LoginAttempt::forLogin($identifier, $loginType)
            ->where('ip_address', $request->ip())
            ->first();
    }

    /**
     * Get or create login attempt record
     */
    private function getOrCreateLoginAttempt(string $identifier, string $loginType, Request $request): LoginAttempt
    {
        return LoginAttempt::firstOrCreate(
            [
                'identifier' => $identifier,
                'login_type' => $loginType,
                'ip_address' => $request->ip()
            ],
            [
                'attempts' => 0,
                'last_attempt_at' => null,
                'locked_until' => null
            ]
        );
    }

    /**
     * Format time in minutes and seconds
     */
    private function formatTime(int $seconds): string
    {
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;
        
        if ($minutes > 0) {
            return $minutes . " minute(s) and " . $remainingSeconds . " second(s)";
        }
        
        return $remainingSeconds . " second(s)";
    }

    /**
     * Clean up expired login attempts (can be called via scheduled task)
     */
    public function cleanupExpiredAttempts(): int
    {
        return LoginAttempt::where('locked_until', '<', now())
            ->where('attempts', '>=', 3)
            ->delete();
    }

    /**
     * Get current lockout status for frontend
     */
    public function getLockoutStatus(string $identifier, string $loginType, Request $request): array
    {
        $loginAttempt = $this->getLoginAttempt($identifier, $loginType, $request);
        
        if (!$loginAttempt || !$loginAttempt->isLocked()) {
            return [
                'locked' => false,
                'remaining_time' => 0
            ];
        }

        return [
            'locked' => true,
            'remaining_time' => $loginAttempt->getRemainingLockoutTime()
        ];
    }
}
