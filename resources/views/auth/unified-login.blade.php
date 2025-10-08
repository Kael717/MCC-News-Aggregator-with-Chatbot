<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Login - MCC News Aggregator</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #111827;
            --primary-light: #1f2937;
            --secondary: #2563eb;
            --secondary-light: #3b82f6;
            --accent: #8b5cf6;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --radius: 12px;
            --radius-sm: 8px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: url("{{ asset('images/mccfront.jpg') }}") no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: var(--gray-800);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            text-size-adjust: 100%;
            touch-action: manipulation;
        }

        .auth-container {
            background: white;
            width: 100%;
            max-width: 480px;
            border-radius: var(--radius);
            box-shadow: var(--shadow-md);
            overflow: hidden;
            transition: var(--transition);
        }

        .logo-container {
            display: flex;
            justify-content: center;
            padding: 1.5rem 0 0;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        }

        .logo {
            height: 90px;
            width: auto;
            border-radius: 50%;
            background: white;
            padding: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .auth-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 1rem 2rem 2.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .auth-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            transform: rotate(30deg);
        }

        .auth-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .auth-header h2 {
            font-size: 1.5rem;
            font-weight: 500;
            margin-bottom: 0.75rem;
        }

        .auth-header p {
            font-size: 1rem;
            opacity: 0.9;
            font-weight: 300;
        }

        .auth-content {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group label {
            display: flex;
            align-items: center;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--gray-700);
        }

        .form-group label i {
            margin-right: 0.75rem;
            color: var(--gray-600);
            width: 16px;
        }

        .login-type-select {
            position: relative;
        }

        .login-type-select select {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3rem;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-sm);
            font-size: 1rem;
            background-color: var(--gray-50);
            appearance: none;
            transition: var(--transition);
            color: var(--gray-800);
            font-weight: 500;
        }

        .login-type-select::before {
            content: '\f0d7';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-600);
            pointer-events: none;
        }

        .login-type-select::after {
            content: '\f0dd';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary);
            z-index: 1;
        }

        .login-type-select select:focus {
            outline: none;
            border-color: var(--secondary-light);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-sm);
            font-size: 1rem;
            transition: var(--transition);
            background-color: white;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            -webkit-tap-highlight-color: transparent;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--secondary-light);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .password-input-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray-600);
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 4px;
            transition: var(--transition);
        }

        .password-toggle:hover {
            color: var(--secondary);
            background-color: var(--gray-100);
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-size: 0.875rem;
            color: var(--gray-700);
        }

        .checkbox-label input {
            margin-right: 0.5rem;
            width: 16px;
            height: 16px;
            accent-color: var(--secondary);
        }

        .btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-light) 100%);
            color: white;
            border: none;
            border-radius: var(--radius-sm);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            justify-content: center;
            align-items: center;
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .btn:hover {
            background: linear-gradient(135deg, var(--secondary-light) 0%, var(--secondary) 100%);
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
        }

        .btn:active {
            transform: translateY(1px);
        }

        .btn i {
            margin-right: 0.5rem;
        }

        .btn:disabled {
            background: var(--gray-300);
            cursor: not-allowed;
            box-shadow: none;
        }

        .btn:disabled:hover {
            transform: none;
        }

        .auth-links {
            margin-top: 1.5rem;
            text-align: center;
            padding-top: 1.5rem;
            border-top: 1px solid var(--gray-200);
        }

        .auth-links a {
            color: var(--secondary);
            text-decoration: none;
            font-size: 0.875rem;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: var(--transition);
        }

        .auth-links a:hover {
            color: var(--secondary-light);
        }

        .auth-links a i {
            margin-right: 0.5rem;
        }

        .error-message {
            background: rgba(239, 68, 68, 0.05);
            color: var(--danger);
            padding: 0.75rem 1rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1rem;
            font-size: 0.875rem;
            border: 1px solid rgba(239, 68, 68, 0.2);
            display: flex;
            align-items: center;
        }

        .error-message::before {
            content: '\f06a';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            margin-right: 0.5rem;
        }

        .success-message {
            background: rgba(16, 185, 129, 0.05);
            color: var(--success);
            padding: 0.75rem 1rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1rem;
            font-size: 0.875rem;
            border: 1px solid rgba(16, 185, 129, 0.2);
            display: flex;
            align-items: center;
        }

        .success-message::before {
            content: '\f058';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            margin-right: 0.5rem;
        }

        .warning-message {
            background: rgba(245, 158, 11, 0.05);
            color: var(--warning);
            padding: 0.75rem 1rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1rem;
            font-size: 0.875rem;
            border: 1px solid rgba(245, 158, 11, 0.2);
            display: flex;
            align-items: center;
        }

        .warning-message::before {
            content: '\f071';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            margin-right: 0.5rem;
        }

        .form-group .error {
            border-color: var(--danger);
        }

        .form-group .error:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
        }

        .forgot-password {
            text-align: center;
            margin: 1rem 0;
        }

        .forgot-password a {
            color: var(--gray-600);
            text-decoration: none;
            font-size: 0.875rem;
            transition: var(--transition);
        }

        .forgot-password a:hover {
            color: var(--secondary);
        }

        /* reCAPTCHA Styling */
        .recaptcha-container {
            display: flex;
            justify-content: center;
            margin: 0.5rem 0;
        }

        .recaptcha-container .g-recaptcha {
            transform: scale(1);
            transform-origin: 0 0;
        }

        /* Animation for form fields */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group {
            animation: fadeIn 0.3s ease forwards;
        }

        /* Enhanced Responsive Design */
        
        /* Extra small devices (phones, 320px and up) */
        @media (max-width: 480px) {
            body {
                padding: 10px;
                min-height: 100vh;
            }
            
            .auth-container {
                max-width: 100%;
                margin: 0;
                border-radius: var(--radius-sm);
                box-shadow: var(--shadow);
            }
            
            .logo-container {
                padding: 1rem 0 0;
            }
            
            .logo {
                height: 70px;
            }
            
            .auth-header {
                padding: 0.75rem 1rem 1.5rem;
            }
            
            .auth-header h1 {
                font-size: 1.75rem;
                margin-bottom: 0.25rem;
            }
            
            .auth-header h2 {
                font-size: 1.125rem;
                margin-bottom: 0.5rem;
            }
            
            .auth-header p {
                font-size: 0.875rem;
            }
            
            .auth-content {
                padding: 1rem;
            }
            
            .form-group {
                margin-bottom: 1.25rem;
            }
            
            .form-group label {
                font-size: 0.8125rem;
                margin-bottom: 0.375rem;
            }
            
            .form-control,
            .login-type-select select {
                padding: 0.75rem 0.875rem;
                font-size: 0.9375rem;
                min-height: 44px; /* Touch-friendly minimum */
            }
            
            .login-type-select select {
                padding-left: 2.75rem;
                padding-right: 2.75rem;
            }
            
            .btn {
                padding: 0.875rem 1rem;
                font-size: 0.9375rem;
                min-height: 44px; /* Touch-friendly minimum */
            }
            
            .password-toggle {
                right: 0.625rem;
                padding: 0.375rem;
            }
            
            .checkbox-label {
                font-size: 0.8125rem;
            }
            
            .checkbox-label input {
                width: 18px;
                height: 18px;
            }
            
            .auth-links a {
                font-size: 0.8125rem;
                padding: 0.5rem;
            }
            
            .forgot-password a {
                font-size: 0.8125rem;
            }
            
            .error-message,
            .success-message,
            .warning-message {
                padding: 0.625rem 0.875rem;
                font-size: 0.8125rem;
            }
            
            /* Mobile reCAPTCHA scaling */
            .recaptcha-container .g-recaptcha {
                transform: scale(0.85);
                transform-origin: 0 0;
            }
        }
        
        /* Small devices (phones, 481px to 576px) */
        @media (min-width: 481px) and (max-width: 576px) {
            body {
                padding: 15px;
            }
            
            .auth-container {
                max-width: 100%;
            }
            
            .logo {
                height: 80px;
            }
            
            .auth-header {
                padding: 1rem 1.25rem 1.75rem;
            }
            
            .auth-header h1 {
                font-size: 2rem;
            }
            
            .auth-header h2 {
                font-size: 1.25rem;
            }
            
            .auth-content {
                padding: 1.25rem;
            }
            
            .form-control,
            .login-type-select select {
                min-height: 46px;
            }
            
            .btn {
                min-height: 46px;
            }
        }
        
        /* Medium devices (tablets, 577px to 768px) */
        @media (min-width: 577px) and (max-width: 768px) {
            .auth-container {
                max-width: 90%;
            }
            
            .auth-header {
                padding: 1.25rem 1.5rem 2rem;
            }
            
            .auth-content {
                padding: 1.75rem;
            }
        }
        
        /* Large devices (desktops, 769px and up) */
        @media (min-width: 769px) {
            .auth-container {
                max-width: 480px;
            }
        }
        
        /* Landscape orientation adjustments for mobile */
        @media (max-width: 768px) and (orientation: landscape) {
            body {
                padding: 10px 20px;
            }
            
            .auth-header {
                padding: 0.75rem 1.5rem 1.25rem;
            }
            
            .auth-header h1 {
                font-size: 1.5rem;
                margin-bottom: 0.25rem;
            }
            
            .auth-header h2 {
                font-size: 1.125rem;
                margin-bottom: 0.25rem;
            }
            
            .auth-header p {
                font-size: 0.875rem;
            }
            
            .logo {
                height: 60px;
            }
            
            .auth-content {
                padding: 1rem 1.5rem;
            }
            
            .form-group {
                margin-bottom: 1rem;
            }
        }
        
        /* High DPI displays */
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .form-control,
            .login-type-select select,
            .btn {
                border-width: 0.5px;
            }
        }
        
        /* Dark mode support (if user prefers dark mode) */
        @media (prefers-color-scheme: dark) {
            .auth-container {
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.15);
            }
        }
        
        /* Reduced motion for accessibility */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
        
        /* Focus improvements for keyboard navigation */
        @media (max-width: 768px) {
            .form-control:focus,
            .login-type-select select:focus,
            .btn:focus {
                box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.25);
                outline: 2px solid transparent;
            }
            
            .password-toggle:focus {
                outline: 2px solid var(--secondary);
                outline-offset: 2px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <!-- Logo at the top center -->
    
        
        <div class="auth-header">
            <h1>MCC-NAC</h1>
            <h2>Login Portal</h2>
            <p>Select your login type and enter your credentials</p>
        </div>

        <div class="auth-content">
            @if(session('success'))
                <div class="success-message" id="swal-success" data-message="{{ session('success') }}" style="display:none"></div>
            @endif

            @if(session('warning'))
                <div class="warning-message">
                    {{ session('warning') }}
                </div>
            @endif


            @if($errors->any())
                <div class="error-message">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <!-- Unified Login Form -->
            <div class="unified-login-form">
                <form method="POST" action="{{ url('/login') }}" id="unified-form">
                    @csrf

                    <!-- Login Type Selector -->
                    <div class="form-group login-type-selector">
                        <label for="login_type">
                            <i class="fas fa-users-cog"></i>
                            Login Type
                        </label>
                        <div class="login-type-select">
                            <select name="login_type" id="login_type" class="form-control" required>
                                <option value="ms365" {{ ($preselectedType ?? session('login_type', 'ms365')) === 'ms365' ? 'selected' : '' }}>
                                    Student/Faculty (MS365)
                                </option>
                                <option value="superadmin" {{ ($preselectedType ?? session('login_type')) === 'superadmin' ? 'selected' : '' }}>
                                    Super Admin
                                </option>
                                <option value="department-admin" {{ ($preselectedType ?? session('login_type')) === 'department-admin' ? 'selected' : '' }}>
                                    Department Admin
                                </option>
                                <option value="office-admin" {{ ($preselectedType ?? session('login_type')) === 'office-admin' ? 'selected' : '' }}>
                                    Office Admin
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Gmail Account Field (for students/faculty) -->
                    <div class="form-group" id="gmail-field" style="display: none;">
                        <label for="gmail_account">
                            <i class="fab fa-google"></i>
                            Gmail Account
                        </label>
                        <input type="email"
                               id="gmail_account"
                               name="gmail_account"
                               class="form-control @error('gmail_account') error @enderror"
                               value="{{ old('gmail_account') }}"
                               placeholder="example@gmail.com"
                               pattern="[a-zA-Z0-9._%+-]+@gmail\.com"
                               title="Please enter a valid Gmail address">
                        @error('gmail_account')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- MS365 Account Field -->
                    <div class="form-group" id="ms365-field" style="display: none;">
                        <label for="ms365_account">
                            <i class="fab fa-microsoft"></i>
                            MS365 Account
                        </label>
                        <input type="email"
                               id="ms365_account"
                               name="ms365_account"
                               class="form-control @error('ms365_account') error @enderror"
                               value="{{ old('ms365_account') }}"
                               placeholder="example@mcc-nac.edu.ph"
                               pattern="[a-zA-Z0-9._%+-]+@.*\.edu\.ph"
                               title="Please enter a valid .edu.ph email address"
                               maxlength="100"
                               data-security-check="true">
                        @error('ms365_account')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Username Field (for admins) -->
                    <div class="form-group" id="username-field" style="display: none;">
                        <label for="username">
                            <i class="fas fa-user"></i>
                            Username
                        </label>
                        <input type="text"
                               id="username"
                               name="username"
                               class="form-control @error('username') error @enderror"
                               value="{{ old('username') }}"
                               placeholder="Enter your username"
                               maxlength="50"
                               pattern="[a-zA-Z0-9_-]+"
                               title="Username can only contain letters, numbers, underscores, and hyphens"
                               data-security-check="true">
                        @error('username')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div class="form-group" id="password-field" style="display: none;">
                        <label for="password">
                            <i class="fas fa-lock"></i>
                            Password
                        </label>
                        <div class="password-input-container">
                            <input type="password"
                                   id="password"
                                   name="password"
                                   class="form-control @error('password') error @enderror"
                                   placeholder="Enter your password"
                                   maxlength="255"
                                   data-security-check="true">
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                <i class="fas fa-eye" id="password-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Forgot Password (MS365) -->
                    <div class="forgot-password" id="forgot-password" style="display:none;">
                        <a href="{{ route('password.request') }}">Forgot Password?</a>
                    </div>

                    <!-- Remember Me -->
                    <div class="form-group" id="remember-field" style="display: none;">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span class="checkbox-text">Remember me</span>
                        </label>
                    </div>

                    <!-- reCAPTCHA -->
                    <div class="form-group" id="recaptcha-field" style="display: none;">
                        <label for="recaptcha">
                            <i class="fas fa-shield-alt"></i>
                            Security Verification
                        </label>
                        <div class="recaptcha-container">
                            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.sitekey') }}"></div>
                        </div>
                        @error('g-recaptcha-response')
                            <div class="error-message">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                        @error('captcha')
                            <div class="error-message">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn" id="submit-btn" disabled>
                        <i class="fas fa-sign-in-alt"></i>
                        Select Login Type
                    </button>
                </form>

                <!-- Auth Links -->
                <div class="auth-links" id="auth-links" style="display: none;">
                    <a href="{{ route('ms365.signup') }}">
                        <i class="fas fa-user-plus"></i>
                        Don't have an MS365 account? Sign up
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.getElementById('swal-success');
            if (el) {
                var msg = el.getAttribute('data-message') || 'Success';
                
                // Determine appropriate title based on message content
                var title = 'Success';
                if (msg.includes('Registration completed') || msg.includes('registered')) {
                    title = 'Registration Complete';
                } else if (msg.includes('logged out')) {
                    title = 'Logged Out';
                } else if (msg.includes('login') || msg.includes('Login')) {
                    title = 'Login Success';
                }
                
                Swal.fire({
                    icon: 'success',
                    title: title,
                    text: msg,
                    confirmButtonColor: '#111827'
                });
            }
        });

        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const eyeIcon = document.getElementById(fieldId + '-eye');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
    <script>
        // Security validation patterns
        const DANGEROUS_PATTERNS = [
            // TypeScript/JavaScript patterns
            /\bfunction\s*\(/i,
            /\bvar\s+/i,
            /\blet\s+/i,
            /\bconst\s+/i,
            /\bclass\s+/i,
            /\binterface\s+/i,
            /\btype\s+/i,
            /\bnamespace\s+/i,
            /\bimport\s+/i,
            /\bexport\s+/i,
            /\brequire\s*\(/i,
            /\bconsole\./i,
            /\balert\s*\(/i,
            /\beval\s*\(/i,
            /\bsetTimeout\s*\(/i,
            /\bsetInterval\s*\(/i,
            // SQL injection patterns
            /\bunion\s+select/i,
            /\bselect\s+.*\bfrom\s+/i,
            /\binsert\s+into/i,
            /\bupdate\s+.*\bset\s+/i,
            /\bdelete\s+from/i,
            /\bdrop\s+table/i,
            /\balter\s+table/i,
            /\bcreate\s+table/i,
            /\btruncate\s+table/i,
            /\bexec\s*\(/i,
            /\bexecute\s*\(/i,
            // Script tags and HTML
            /<script[^>]*>/i,
            /<\/script>/i,
            /<iframe[^>]*>/i,
            /<object[^>]*>/i,
            /<embed[^>]*>/i,
            /<link[^>]*>/i,
            /<meta[^>]*>/i,
            // PHP patterns
            /<\?php/i,
            /<\?=/i,
            /\bphp:/i,
            // Command injection
            /\bsystem\s*\(/i,
            /\bexec\s*\(/i,
            /\bshell_exec\s*\(/i,
            /\bpassthru\s*\(/i,
            // Other dangerous patterns
            /javascript:/i,
            /vbscript:/i,
            /data:text\/html/i,
            /\bon\w+\s*=/i, // event handlers like onclick=
            /\\\x[0-9a-f]{2}/i, // hex encoding
            /\\\u[0-9a-f]{4}/i, // unicode encoding
        ];

        function validateInput(value, fieldName) {
            if (!value) return { valid: true };

            // Check for dangerous patterns
            for (let pattern of DANGEROUS_PATTERNS) {
                if (pattern.test(value)) {
                    return {
                        valid: false,
                        message: `Invalid characters detected in ${fieldName}. Please use only standard alphanumeric characters.`
                    };
                }
            }

            // Additional length checks
            if (value.length > 255) {
                return {
                    valid: false,
                    message: `${fieldName} is too long. Maximum 255 characters allowed.`
                };
            }

            // Check for excessive special characters (potential obfuscation)
            const specialCharCount = (value.match(/[^a-zA-Z0-9@._-\s]/g) || []).length;
            if (specialCharCount > value.length * 0.3) {
                return {
                    valid: false,
                    message: `${fieldName} contains too many special characters.`
                };
            }

            return { valid: true };
        }

        function showSecurityError(message) {
            // Remove existing security error
            const existingError = document.querySelector('.security-error-message');
            if (existingError) {
                existingError.remove();
            }

            // Create new error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message security-error-message';
            errorDiv.textContent = message;
            
            // Insert at the top of the form
            const form = document.getElementById('unified-form');
            form.insertBefore(errorDiv, form.firstChild);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (errorDiv.parentNode) {
                    errorDiv.remove();
                }
            }, 5000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const loginTypeSelect = document.getElementById('login_type');
            const gmailField = document.getElementById('gmail-field');
            const ms365Field = document.getElementById('ms365-field');
            const usernameField = document.getElementById('username-field');
            const passwordField = document.getElementById('password-field');
            const rememberField = document.getElementById('remember-field');
            const submitBtn = document.getElementById('submit-btn');
            const authLinks = document.getElementById('auth-links');
            const forgotPassword = document.getElementById('forgot-password');

            // Add real-time validation to security-checked inputs
            document.querySelectorAll('[data-security-check="true"]').forEach(input => {
                input.addEventListener('input', function() {
                    const validation = validateInput(this.value, this.name);
                    if (!validation.valid) {
                        this.classList.add('error');
                        showSecurityError(validation.message);
                        this.value = ''; // Clear the dangerous input
                    } else {
                        this.classList.remove('error');
                    }
                });

                // Prevent paste of dangerous content
                input.addEventListener('paste', function(e) {
                    setTimeout(() => {
                        const validation = validateInput(this.value, this.name);
                        if (!validation.valid) {
                            this.classList.add('error');
                            showSecurityError(validation.message);
                            this.value = ''; // Clear the dangerous input
                            e.preventDefault();
                        }
                    }, 10);
                });
            });

            // Enhanced form submission validation
            document.getElementById('unified-form').addEventListener('submit', function(e) {
                const formData = new FormData(this);
                let hasErrors = false;

                // Validate all form inputs
                for (let [key, value] of formData.entries()) {
                    // Skip CSRF token and Google reCAPTCHA payload from client-side checks
                    if (key !== '_token' && key !== 'g-recaptcha-response' && value) {
                        const validation = validateInput(value, key);
                        if (!validation.valid) {
                            e.preventDefault();
                            showSecurityError(validation.message);
                            hasErrors = true;
                            break;
                        }
                    }
                }

                if (hasErrors) {
                    return false;
                }
            });

            function toggleFields() {
                const selectedType = loginTypeSelect.value;
                console.log('Selected login type:', selectedType); // Debug log

                // Hide all fields initially
                gmailField.style.display = 'none';
                ms365Field.style.display = 'none';
                usernameField.style.display = 'none';
                passwordField.style.display = 'none';
                rememberField.style.display = 'none';
                authLinks.style.display = 'none';
                forgotPassword.style.display = 'none';

                // Clear fields and error classes when switching types
                function clearGroup(groupIds) {
                    groupIds.forEach(function (id) {
                        var el = document.getElementById(id);
                        if (!el) return;
                        var inputs = el.querySelectorAll('input');
                        inputs.forEach(function (i) {
                            if (i.type === 'checkbox') { 
                                i.checked = false; 
                            } else { 
                                i.value = ''; 
                            }
                            i.classList.remove('error');
                        });
                    });
                }

                // Set required attribute for fields
                function setRequired(elId, required) {
                    var el = document.getElementById(elId);
                    if (el) { 
                        el.required = !!required; 
                    }
                }

                // Remove all required flags initially
                setRequired('gmail_account', false);
                setRequired('ms365_account', false);
                setRequired('username', false);
                setRequired('password', false);

                // Show fields based on selection
                if (selectedType === 'ms365') {
                    // Clear admin fields
                    clearGroup(['username-field']);
                    
                    // Show student/faculty fields
                    ms365Field.style.display = 'block';
                    passwordField.style.display = 'block';
                    rememberField.style.display = 'block';
                    authLinks.style.display = 'block';
                    forgotPassword.style.display = 'block';
                    
                    // Show reCAPTCHA
                    const recaptchaField = document.getElementById('recaptcha-field');
                    if (recaptchaField) {
                        recaptchaField.style.display = 'block';
                    }
                    
                    console.log('Forgot password should now be visible'); // Debug log
                    
                    // Set required fields
                    setRequired('ms365_account', true);
                    setRequired('password', true);
                    
                    // Update button text
                    submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Login with MS365';
                    submitBtn.disabled = false;
                    
                } else if (selectedType === 'superadmin') {
                    // Clear student fields
                    clearGroup(['ms365-field', 'gmail-field']);
                    
                    // Show admin fields (superadmin uses username)
                    usernameField.style.display = 'block';
                    passwordField.style.display = 'block';
                    rememberField.style.display = 'block';
                    
                    // Show reCAPTCHA
                    const recaptchaField = document.getElementById('recaptcha-field');
                    if (recaptchaField) {
                        recaptchaField.style.display = 'block';
                    }
                    
                    // Set required fields
                    setRequired('username', true);
                    setRequired('password', true);
                    
                    // Update button text
                    submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Login as Super Admin';
                    submitBtn.disabled = false;
                    
                } else if (selectedType === 'department-admin' || selectedType === 'office-admin') {
                    // Clear username fields
                    clearGroup(['username-field', 'gmail-field']);
                    
                    // Show MS365 fields for department and office admins
                    ms365Field.style.display = 'block';
                    passwordField.style.display = 'block';
                    rememberField.style.display = 'block';
                    
                    // Show reCAPTCHA
                    const recaptchaField = document.getElementById('recaptcha-field');
                    if (recaptchaField) {
                        recaptchaField.style.display = 'block';
                    }
                    
                    // Set required fields
                    setRequired('ms365_account', true);
                    setRequired('password', true);
                    
                    // Update button text
                    const adminType = selectedType === 'department-admin' ? 'Department Admin' : 'Office Admin';
                    submitBtn.innerHTML = `<i class="fas fa-sign-in-alt"></i> Login as ${adminType}`;
                    submitBtn.disabled = false;
                    
                } else {
                    submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Select Login Type';
                    submitBtn.disabled = true;
                }
            }

            // Initial call to set the correct state
            console.log('Initializing form fields...'); // Debug log
            toggleFields();

            // Add event listener for changes
            loginTypeSelect.addEventListener('change', toggleFields);
            
            // Force show forgot password link for MS365 (fallback)
            setTimeout(function() {
                if (loginTypeSelect.value === 'ms365') {
                    const forgotPasswordElement = document.getElementById('forgot-password');
                    if (forgotPasswordElement) {
                        forgotPasswordElement.style.display = 'block';
                        console.log('Forced forgot password to show'); // Debug log
                    }
                }
            }, 100);
        });

    </script>

    <!-- reCAPTCHA JavaScript -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>
</html>