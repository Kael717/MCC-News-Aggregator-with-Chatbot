<?php

namespace App\Http\Middleware;

use App\Helpers\CSPHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // HTTP Strict Transport Security (HSTS)
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');

        // Referrer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions Policy (formerly Feature Policy)
        $response->headers->set('Permissions-Policy', 
            'geolocation=(), microphone=(), camera=(), payment=(), usb=(), ' .
            'magnetometer=(), gyroscope=(), speaker=(), vibrate=(), ' .
            'fullscreen=(self), sync-xhr=()'
        );

        // Secure Content Security Policy (CSP) with nonces
        $response->headers->set('Content-Security-Policy', CSPHelper::generateCSP());

        // Additional Security Headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        
        // Cross-Origin Policies (relaxed to allow external styles/fonts/CDNs on dashboard)
        $response->headers->set('Cross-Origin-Embedder-Policy', 'unsafe-none');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'cross-origin');

        // Remove server information
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}
