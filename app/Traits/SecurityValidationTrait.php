<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait SecurityValidationTrait
{
    /**
     * Validate input for dangerous patterns to prevent SQL injection and code injection
     */
    protected function validateSecureInput(Request $request)
    {
        $dangerousPatterns = $this->getDangerousPatterns();

        // Check all input fields for dangerous patterns
        foreach ($request->all() as $key => $value) {
            // Skip CSRF token and reCAPTCHA payload from generic pattern/length checks
            if ($key !== '_token' && $key !== 'g-recaptcha-response' && is_string($value) && !empty($value)) {
                foreach ($dangerousPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        \Log::warning('Dangerous pattern detected in authentication form', [
                            'field' => $key,
                            'pattern' => $pattern,
                            'ip' => $request->ip(),
                            'user_agent' => $request->userAgent(),
                            'controller' => get_class($this),
                        ]);
                        
                        abort(422, 'Invalid input detected. Please use only standard alphanumeric characters.');
                    }
                }

                // Check for excessive special characters (potential obfuscation)
                $specialCharCount = preg_match_all('/[^a-zA-Z0-9@._\-\s]/', $value);
                if ($specialCharCount > strlen($value) * 0.3) {
                    \Log::warning('Excessive special characters detected in authentication form', [
                        'field' => $key,
                        'special_char_ratio' => $specialCharCount / strlen($value),
                        'ip' => $request->ip(),
                        'controller' => get_class($this),
                    ]);
                    
                    abort(422, 'Input contains too many special characters.');
                }
            }
        }
    }

    /**
     * Check if input contains dangerous patterns
     */
    protected function containsDangerousPatterns($value)
    {
        if (empty($value)) {
            return false;
        }

        $dangerousPatterns = $this->getDangerousPatterns();

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get array of dangerous patterns for validation
     */
    private function getDangerousPatterns()
    {
        return [
            // TypeScript/JavaScript patterns
            '/\bfunction\s*\(/i',
            '/\bvar\s+/i',
            '/\blet\s+/i',
            '/\bconst\s+/i',
            '/\bclass\s+/i',
            '/\binterface\s+/i',
            '/\btype\s+/i',
            '/\bnamespace\s+/i',
            '/\bimport\s+/i',
            '/\bexport\s+/i',
            '/\brequire\s*\(/i',
            '/\bconsole\./i',
            '/\balert\s*\(/i',
            '/\beval\s*\(/i',
            '/\bsetTimeout\s*\(/i',
            '/\bsetInterval\s*\(/i',
            // SQL injection patterns
            '/\bunion\s+select/i',
            '/\bselect\s+.*\bfrom\s+/i',
            '/\binsert\s+into/i',
            '/\bupdate\s+.*\bset\s+/i',
            '/\bdelete\s+from/i',
            '/\bdrop\s+table/i',
            '/\balter\s+table/i',
            '/\bcreate\s+table/i',
            '/\btruncate\s+table/i',
            '/\bexec\s*\(/i',
            '/\bexecute\s*\(/i',
            // Script tags and HTML
            '/<script[^>]*>/i',
            '/<\/script>/i',
            '/<iframe[^>]*>/i',
            '/<object[^>]*>/i',
            '/<embed[^>]*>/i',
            '/<link[^>]*>/i',
            '/<meta[^>]*>/i',
            // PHP patterns
            '/<\?php/i',
            '/<\?=/i',
            '/\bphp:/i',
            // Command injection
            '/\bsystem\s*\(/i',
            '/\bexec\s*\(/i',
            '/\bshell_exec\s*\(/i',
            '/\bpassthru\s*\(/i',
            // Other dangerous patterns
            '/javascript:/i',
            '/vbscript:/i',
            '/data:text\/html/i',
            '/\bon\w+\s*=/i', // event handlers like onclick=
            '/\\\x[0-9a-f]{2}/i', // hex encoding
            '/\\\u[0-9a-f]{4}/i', // unicode encoding
        ];
    }

    /**
     * Get secure validation rules for common authentication fields
     */
    protected function getSecureValidationRules()
    {
        return [
            'username' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9_-]+$/',
                function ($attribute, $value, $fail) {
                    if ($value && $this->containsDangerousPatterns($value)) {
                        $fail('Invalid characters detected in username.');
                    }
                },
            ],
            'ms365_account' => [
                'nullable',
                'email',
                'max:100',
                'regex:/^[a-zA-Z0-9._%+-]+@.*\.edu\.ph$/',
                function ($attribute, $value, $fail) {
                    if ($value && $this->containsDangerousPatterns($value)) {
                        $fail('Invalid characters detected in email address.');
                    }
                },
            ],
            'gmail_account' => [
                'nullable',
                'email',
                'max:100',
                'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/',
                function ($attribute, $value, $fail) {
                    if ($value && $this->containsDangerousPatterns($value)) {
                        $fail('Invalid characters detected in email address.');
                    }
                },
            ],
            'password' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if ($value && $this->containsDangerousPatterns($value)) {
                        $fail('Invalid characters detected in password.');
                    }
                },
            ],
        ];
    }

    /**
     * Get secure validation messages
     */
    protected function getSecureValidationMessages()
    {
        return [
            'username.regex' => 'Username can only contain letters, numbers, underscores, and hyphens',
            'ms365_account.regex' => 'Please enter a valid .edu.ph email address',
            'gmail_account.regex' => 'Please enter a valid Gmail address',
        ];
    }
}
