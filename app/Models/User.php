<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'middle_name',
        'surname',
        'ms365_account',
        'gmail_account',
        'full_name',
        'password',
        'role',
        'department',
        'year_level',
        'profile_picture',
        'email_verified_at',
        'ms365_account',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'email_verified_at' => 'datetime',
        'ms365_account' => 'string',
    ];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . ($this->middle_name ? $this->middle_name . ' ' : '') . $this->surname;
    }

    /**
     * Get the user's name (alias for full_name for compatibility)
     */
    public function getNameAttribute()
    {
        return $this->getFullNameAttribute();
    }

    /**
     * Get the email address that should be used for password resets.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->ms365_account ?? $this->gmail_account;
    }

    /**
     * Route notifications for the mail channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return array|string
     */
    public function routeNotificationForMail($notification)
    {
        // For password reset notifications, ensure we use the correct email
        if (method_exists($notification, 'toMail')) {
            return $this->ms365_account ?? $this->gmail_account;
        }
        return $this->ms365_account ?? $this->gmail_account;
    }

    /**
     * Get the email attribute for password resets (Laravel's default method)
     */
    public function getEmailAttribute()
    {
        return $this->ms365_account ?? $this->gmail_account;
    }

    /**
     * Get the primary email address (ms365_account takes precedence over gmail_account)
     */
    public function getPrimaryEmailAttribute()
    {
        return $this->ms365_account ?: $this->gmail_account;
    }

    /**
     * Get the notifications for the user.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get unread notifications count.
     */
    public function getUnreadNotificationsCountAttribute()
    {
        return $this->notifications()->unread()->count();
    }

    /**
     * Get the profile picture URL.
     */
    public function getProfilePictureUrlAttribute()
    {
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }
        return null;
    }

    /**
     * Check if user has a profile picture.
     */
    public function getHasProfilePictureAttribute()
    {
        return !empty($this->profile_picture);
    }

    /**
     * Get user initials for avatar fallback.
     */
    public function getInitialsAttribute()
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->surname, 0, 1));
    }
}