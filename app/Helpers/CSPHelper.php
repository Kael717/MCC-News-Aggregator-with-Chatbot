<?php

namespace App\Helpers;

class CSPHelper
{
    /**
     * Generate a cryptographically secure nonce
     */
    public static function generateNonce(): string
    {
        return base64_encode(random_bytes(16));
    }

    /**
     * Get the current nonce for the request
     */
    public static function getNonce(): string
    {
        if (!session()->has('csp_nonce')) {
            session(['csp_nonce' => self::generateNonce()]);
        }
        
        return session('csp_nonce');
    }

    /**
     * Generate secure CSP header
     */
    public static function generateCSP(): string
    {
        $nonce = self::getNonce();
        
        return implode('; ', [
            "default-src 'self'",
            // Allow Google reCAPTCHA and required domains
            "script-src 'self' 'nonce-{$nonce}' https://www.google.com https://www.gstatic.com https://www.recaptcha.net https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://cdn.tailwindcss.com https://fonts.googleapis.com",
            "style-src 'self' 'nonce-{$nonce}' https://cdnjs.cloudflare.com https://fonts.googleapis.com https://cdn.jsdelivr.net",
            "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com",
            "img-src 'self' data: https:",
            "connect-src 'self' https://www.google.com https://www.recaptcha.net",
            // Allow embedding of reCAPTCHA v3 badge iframe
            "frame-src 'self' https://www.google.com https://www.gstatic.com https://www.recaptcha.net",
            "child-src 'self' https://www.google.com https://www.gstatic.com https://www.recaptcha.net",
            "frame-ancestors 'none'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'"
        ]);
    }
}
