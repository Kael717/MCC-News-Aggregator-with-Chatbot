<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
        'ms365_account',
        'gmail_account',
        'username',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            $this->logSecurityException($e);
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Handle security-related exceptions
        if ($this->isSecurityException($e)) {
            return $this->handleSecurityException($request, $e);
        }

        // Handle validation exceptions with security logging
        if ($e instanceof ValidationException) {
            $this->logValidationException($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Check if exception is security-related
     */
    private function isSecurityException(Throwable $e): bool
    {
        $securityMessages = [
            'Invalid input detected',
            'Dangerous pattern detected',
            'Suspicious pattern detected',
            'Rate limit exceeded',
            'Invalid request headers',
            'Request entity too large',
        ];

        $message = $e->getMessage();
        foreach ($securityMessages as $securityMessage) {
            if (strpos($message, $securityMessage) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Handle security exceptions
     */
    private function handleSecurityException(Request $request, Throwable $e)
    {
        $this->logSecurityException($e);

        // Return generic error message to prevent information disclosure
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'An error occurred. Please try again.',
                'error' => 'SECURITY_ERROR'
            ], 422);
        }

        return redirect()->back()
            ->withErrors(['security' => 'An error occurred. Please try again.'])
            ->withInput($request->except(['password', 'password_confirmation']));
    }

    /**
     * Log security exceptions
     */
    private function logSecurityException(Throwable $e): void
    {
        Log::channel('security')->warning('Security exception occurred', [
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Log validation exceptions
     */
    private function logValidationException(Request $request, ValidationException $e): void
    {
        Log::channel('auth')->info('Validation failed', [
            'errors' => $e->errors(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'timestamp' => now()->toISOString(),
        ]);
    }
}
