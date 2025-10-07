<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Admin Registration - MCC News Aggregator</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .registration-container {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            position: relative;
            overflow: hidden;
        }

        .registration-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #333;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .header h1 i {
            color: #667eea;
            font-size: 2.2rem;
        }

        .header p {
            color: #666;
            font-size: 1rem;
            margin-bottom: 5px;
        }

        .department-badge {
            display: inline-block;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-top: 10px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid rgba(102, 126, 234, 0.2);
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
            color: #333;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: white;
        }

        .form-control.error {
            border-color: #ef4444;
            background-color: #fef2f2;
        }

        .password-field {
            position: relative;
        }

        .password-field .form-control {
            padding-right: 55px;
        }

        .password-toggle {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            z-index: 10;
            padding: 5px;
        }

        .password-toggle:hover {
            color: #667eea;
            transform: translateY(-50%) scale(1.1);
        }

        .password-strength {
            margin-top: 10px;
            padding: 10px;
            border-radius: 8px;
            font-size: 0.85rem;
            display: none;
        }

        .password-strength.weak {
            background-color: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
            display: block;
        }

        .password-strength.medium {
            background-color: #fef3c7;
            color: #d97706;
            border: 1px solid #fed7aa;
            display: block;
        }

        .password-strength.strong {
            background-color: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
            display: block;
        }

        .password-requirements {
            margin-top: 10px;
            padding: 15px;
            background-color: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .password-requirements h4 {
            color: #374151;
            font-size: 0.9rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .requirement {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 5px;
            font-size: 0.85rem;
            color: #6b7280;
        }

        .requirement i {
            width: 16px;
            text-align: center;
        }

        .requirement.met {
            color: #16a34a;
        }

        .requirement.met i {
            color: #16a34a;
        }

        .error-message {
            color: #ef4444;
            font-size: 0.85rem;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            margin-bottom: 15px;
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-secondary {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            border: 2px solid rgba(102, 126, 234, 0.2);
        }

        .btn-secondary:hover {
            background: rgba(102, 126, 234, 0.2);
            transform: translateY(-1px);
        }

        .form-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .form-footer p {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .form-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .registration-container {
                padding: 30px 20px;
                margin: 10px;
            }

            .header h1 {
                font-size: 1.7rem;
            }

            .header h1 i {
                font-size: 1.9rem;
            }
        }

        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <div class="header">
            <h1><i class="fas fa-user-shield"></i> Admin Registration</h1>
            <p>Complete your department admin account setup</p>
            <div class="department-badge">{{ $department }} Department</div>
            <div style="margin-top: 15px; padding: 10px; background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); border-radius: 8px; font-size: 0.85rem; color: #16a34a;">
                <i class="fas fa-shield-alt"></i> <strong>Secure Registration</strong> - This link is protected with advanced security tokens and expires in 30 minutes.
            </div>
        </div>

        <form id="registrationForm" action="{{ route('admin.register.complete') }}" method="POST">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <input type="hidden" name="department" value="{{ $department }}">
            <input type="hidden" name="secure_token" value="{{ $token }}">
            <input type="hidden" name="timestamp" value="{{ $timestamp }}">

            <div class="form-group">
                <label for="email_display">Email Address</label>
                <input type="email" 
                       id="email_display" 
                       class="form-control" 
                       value="{{ $email }}" 
                       readonly
                       style="background-color: #f8fafc; cursor: not-allowed;">
            </div>


            <div class="form-group">
                <label for="password">Password <span style="color: red;">*</span></label>
                <div class="password-field">
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control @error('password') error @enderror" 
                           placeholder="Create a strong password"
                           required>
                    <i class="fas fa-eye password-toggle" onclick="togglePassword('password', this)"></i>
                </div>
                <div id="passwordStrength" class="password-strength"></div>
                <div class="password-requirements">
                    <h4><i class="fas fa-shield-alt"></i> Password Requirements</h4>
                    <div class="requirement" id="req-length">
                        <i class="fas fa-times"></i>
                        At least 8 characters long
                    </div>
                    <div class="requirement" id="req-uppercase">
                        <i class="fas fa-times"></i>
                        One uppercase letter (A-Z)
                    </div>
                    <div class="requirement" id="req-lowercase">
                        <i class="fas fa-times"></i>
                        One lowercase letter (a-z)
                    </div>
                    <div class="requirement" id="req-number">
                        <i class="fas fa-times"></i>
                        One number (0-9)
                    </div>
                    <div class="requirement" id="req-special">
                        <i class="fas fa-times"></i>
                        One special character (@$!%*?&)
                    </div>
                </div>
                @error('password')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password <span style="color: red;">*</span></label>
                <div class="password-field">
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           class="form-control @error('password_confirmation') error @enderror" 
                           placeholder="Confirm your password"
                           required>
                    <i class="fas fa-eye password-toggle" onclick="togglePassword('password_confirmation', this)"></i>
                </div>
                <div id="passwordMatch" class="password-strength"></div>
                @error('password_confirmation')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary" id="submitBtn">
                <i class="fas fa-user-plus"></i>
                <span id="submitText">Create Admin Account</span>
                <div class="loading-spinner" id="loadingSpinner"></div>
            </button>

        </form>
    </div>

    <script>
        // Password visibility toggle
        function togglePassword(fieldId, icon) {
            const field = document.getElementById(fieldId);
            const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
            field.setAttribute('type', type);
            
            // Toggle icon
            if (type === 'text') {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Password strength checker
        function checkPasswordStrength(password) {
            const requirements = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /\d/.test(password),
                special: /[@$!%*?&]/.test(password)
            };

            // Update requirement indicators
            updateRequirement('req-length', requirements.length);
            updateRequirement('req-uppercase', requirements.uppercase);
            updateRequirement('req-lowercase', requirements.lowercase);
            updateRequirement('req-number', requirements.number);
            updateRequirement('req-special', requirements.special);

            const metCount = Object.values(requirements).filter(Boolean).length;
            const strengthDiv = document.getElementById('passwordStrength');

            if (password.length === 0) {
                strengthDiv.style.display = 'none';
                return false;
            }

            if (metCount < 3) {
                strengthDiv.className = 'password-strength weak';
                strengthDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Weak password - Please meet more requirements';
                return false;
            } else if (metCount < 5) {
                strengthDiv.className = 'password-strength medium';
                strengthDiv.innerHTML = '<i class="fas fa-shield-alt"></i> Medium strength - Consider meeting all requirements';
                return false;
            } else {
                strengthDiv.className = 'password-strength strong';
                strengthDiv.innerHTML = '<i class="fas fa-check-circle"></i> Strong password!';
                return true;
            }
        }

        function updateRequirement(id, met) {
            const element = document.getElementById(id);
            const icon = element.querySelector('i');
            
            if (met) {
                element.classList.add('met');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-check');
            } else {
                element.classList.remove('met');
                icon.classList.remove('fa-check');
                icon.classList.add('fa-times');
            }
        }

        // Password confirmation checker
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmation = document.getElementById('password_confirmation').value;
            const matchDiv = document.getElementById('passwordMatch');

            if (confirmation.length === 0) {
                matchDiv.style.display = 'none';
                return false;
            }

            if (password === confirmation) {
                matchDiv.className = 'password-strength strong';
                matchDiv.innerHTML = '<i class="fas fa-check-circle"></i> Passwords match!';
                return true;
            } else {
                matchDiv.className = 'password-strength weak';
                matchDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Passwords do not match';
                return false;
            }
        }

        // Form validation
        function validateForm() {
            const password = document.getElementById('password').value;
            const confirmation = document.getElementById('password_confirmation').value;
            const submitBtn = document.getElementById('submitBtn');

            const isPasswordStrong = checkPasswordStrength(password);
            const isPasswordMatch = checkPasswordMatch();
            const isValid = isPasswordStrong && isPasswordMatch && password.length > 0 && confirmation.length > 0;

            submitBtn.disabled = !isValid;
            return isValid;
        }

        // Event listeners
        document.getElementById('password').addEventListener('input', function() {
            validateForm();
        });

        document.getElementById('password_confirmation').addEventListener('input', function() {
            validateForm();
        });

        // Security check to prevent form tampering
        function validateSecurityTokens() {
            const secureToken = document.querySelector('input[name="secure_token"]').value;
            const timestamp = document.querySelector('input[name="timestamp"]').value;
            const email = document.querySelector('input[name="email"]').value;
            const department = document.querySelector('input[name="department"]').value;

            if (!secureToken || !timestamp || !email || !department) {
                Swal.fire({
                    icon: 'error',
                    title: 'Security Error',
                    text: 'Security tokens are missing or invalid. Please request a new registration link.',
                    confirmButtonColor: '#667eea'
                }).then(() => {
                    window.location.href = '{{ route("login") }}';
                });
                return false;
            }

            // Check if timestamp is too old (30 minutes)
            const currentTime = Math.floor(Date.now() / 1000);
            const tokenTime = parseInt(timestamp);
            if (currentTime - tokenTime > 1800) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Registration Expired',
                    text: 'This registration link has expired. Please request a new registration link.',
                    confirmButtonColor: '#667eea'
                }).then(() => {
                    window.location.href = '{{ route("login") }}';
                });
                return false;
            }

            return true;
        }

        // Form submission with loading state and security validation
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const loadingSpinner = document.getElementById('loadingSpinner');

            // Security validation first
            if (!validateSecurityTokens()) {
                e.preventDefault();
                return;
            }

            if (!validateForm()) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Form',
                    text: 'Please ensure your password meets all requirements and passwords match.',
                    confirmButtonColor: '#667eea'
                });
                return;
            }

            // Show loading state
            submitBtn.disabled = true;
            submitText.textContent = 'Creating Account...';
            loadingSpinner.style.display = 'block';
        });

        // Show success message if there are no errors
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#667eea'
            });
        @endif

        // Show error message if there are errors
        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Registration Failed',
                html: '@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach',
                confirmButtonColor: '#667eea'
            });
        @endif

        // Initial validation and security check
        document.addEventListener('DOMContentLoaded', function() {
            // Validate security tokens on page load
            if (!validateSecurityTokens()) {
                return;
            }
            
            // Initial form validation
            validateForm();

            // Add periodic security check (every 5 minutes)
            setInterval(function() {
                const timestamp = document.querySelector('input[name="timestamp"]').value;
                const currentTime = Math.floor(Date.now() / 1000);
                const tokenTime = parseInt(timestamp);
                
                if (currentTime - tokenTime > 1800) { // 30 minutes
                    Swal.fire({
                        icon: 'warning',
                        title: 'Session Expired',
                        text: 'Your registration session has expired for security reasons. Please request a new registration link.',
                        confirmButtonColor: '#667eea',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then(() => {
                        window.location.href = '{{ route("login") }}';
                    });
                }
            }, 300000); // Check every 5 minutes
        });
    </script>
</body>
</html>
