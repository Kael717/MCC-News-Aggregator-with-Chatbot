<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains security-related configuration options for the
    | application to prevent injection attacks and enhance security.
    |
    */

    'injection_prevention' => [
        // SQL Injection Prevention
        'sql_injection' => [
            'enabled' => true,
            'use_prepared_statements' => true,
            'validate_input' => true,
            'escape_output' => true,
        ],

        // XSS Prevention
        'xss_prevention' => [
            'enabled' => true,
            'escape_html' => true,
            'escape_javascript' => true,
            'content_security_policy' => true,
        ],

        // Command Injection Prevention
        'command_injection' => [
            'enabled' => true,
            'whitelist_commands' => [],
            'escape_arguments' => true,
            'validate_input' => true,
        ],

        // File Upload Security
        'file_upload' => [
            'enabled' => true,
            'max_size' => 5 * 1024 * 1024, // 5MB
            'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'],
            'allowed_mimes' => [
                'image/jpeg',
                'image/png', 
                'image/gif',
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ],
            'scan_uploads' => true,
            'quarantine_suspicious' => true,
        ],

        // XXE Prevention
        'xxe_prevention' => [
            'enabled' => true,
            'disable_external_entities' => true,
            'disable_dtd' => true,
            'validate_xml' => true,
        ],
    ],

    'rate_limiting' => [
        'enabled' => true,
        'default_attempts' => 10,
        'default_decay_minutes' => 1,
        'endpoints' => [
            'login' => ['attempts' => 5, 'decay_minutes' => 1],
            'password_reset' => ['attempts' => 3, 'decay_minutes' => 5],
            'registration' => ['attempts' => 3, 'decay_minutes' => 5],
        ],
    ],

    'input_validation' => [
        'enabled' => true,
        'max_length' => 1000,
        'min_length' => 1,
        'allowed_characters' => [
            'username' => '/^[a-zA-Z0-9_-]+$/',
            'email' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            'password' => '/^.{8,}$/',
        ],
        'dangerous_patterns' => [
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
            
            // Script injection patterns
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
            '/\bon\w+\s*=/i',
            '/\\\x[0-9a-f]{2}/i',
            '/\\\u[0-9a-f]{4}/i',
        ],
    ],

    'logging' => [
        'enabled' => true,
        'log_level' => 'info',
        'log_security_events' => true,
        'log_failed_attempts' => true,
        'log_suspicious_activity' => true,
        'retention_days' => 30,
    ],

    'monitoring' => [
        'enabled' => true,
        'alert_on_multiple_failures' => true,
        'alert_threshold' => 5,
        'alert_timeframe_minutes' => 10,
        'notify_admins' => true,
    ],

    'headers' => [
        'security_headers' => [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'X-XSS-Protection' => '1; mode=block',
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
            'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline' https://www.google.com https://www.gstatic.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self';",
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
        ],
    ],

    'database' => [
        'use_prepared_statements' => true,
        'escape_queries' => true,
        'validate_connections' => true,
        'connection_timeout' => 30,
        'max_connections' => 100,
    ],

    'session' => [
        'secure' => env('SESSION_SECURE', true),
        'http_only' => true,
        'same_site' => 'strict',
        'lifetime' => 120, // minutes
        'regenerate_on_login' => true,
        'regenerate_on_logout' => true,
    ],

    'cookies' => [
        'secure' => env('COOKIE_SECURE', true),
        'http_only' => true,
        'same_site' => 'strict',
        'encrypt' => true,
    ],

    'encryption' => [
        'enabled' => true,
        'algorithm' => 'AES-256-CBC',
        'key_rotation' => false,
        'key_rotation_days' => 90,
    ],

    'backup' => [
        'enabled' => true,
        'frequency' => 'daily',
        'retention_days' => 30,
        'encrypt_backups' => true,
        'test_restore' => true,
    ],
];
