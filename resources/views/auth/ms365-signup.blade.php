<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MS365 Sign Up - MCC News Aggregator</title>
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
    background: url('{{ asset("images/mccfront.jpg") }}') no-repeat center center fixed;
    background-size: cover;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
    color: var(--gray-800);
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

        .auth-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 2.5rem 2rem;
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

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-sm);
            font-size: 1rem;
            transition: var(--transition);
            background-color: white;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--secondary-light);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
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

        .form-group .error {
            border-color: var(--danger);
        }

        .form-group .error:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
        }

        .form-help {
            display: flex;
            align-items: center;
            margin-top: 0.5rem;
            font-size: 0.75rem;
            color: var(--gray-600);
        }

        .form-help i {
            margin-right: 0.5rem;
        }

        .info-section {
            margin-top: 2rem;
            padding: 1.5rem;
            background: var(--gray-50);
            border-radius: var(--radius-sm);
            border-left: 4px solid var(--secondary);
        }

        .info-section h3 {
            display: flex;
            align-items: center;
            font-size: 1rem;
            margin-bottom: 1rem;
            color: var(--gray-800);
        }

        .info-section h3 i {
            margin-right: 0.5rem;
            color: var(--secondary);
        }

        .info-section ol {
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }

        .info-section li {
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            color: var(--gray-700);
        }

        .security-note {
            padding: 0.75rem 1rem;
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
            border-radius: var(--radius-sm);
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        .security-note i {
            margin-right: 0.5rem;
            color: var(--warning);
        }

        /* Animation for form fields */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group {
            animation: fadeIn 0.3s ease forwards;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .auth-container {
                max-width: 100%;
            }
            
            .auth-header {
                padding: 2rem 1.5rem;
            }
            
            .auth-content {
                padding: 1.5rem;
            }
            
            .auth-header h1 {
                font-size: 2rem;
            }
            
            .auth-header h2 {
                font-size: 1.25rem;
            }
            
            .info-section {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <h1>MCC-NAC</h1>
            <h2>MS365 Sign Up</h2>
            <p>Enter your MS365 address to get started</p>
        </div>

        <div class="auth-content">
            @if(session('success'))
                <div id="swal-success" data-message="{{ session('success') }}" style="display:none;"></div>
            @endif

            <form method="POST" action="{{ route('ms365.signup.send') }}">
                @csrf

                <div class="form-group">
                    <label for="ms365_account">
                        <i class="fab fa-microsoft"></i>
                        MS365 Email Address
                    </label>
                    <input type="email"
                           id="ms365_account"
                           name="ms365_account"
                           class="form-control @error('ms365_account') error @enderror"
                           value="{{ old('ms365_account') }}"
                           placeholder="your.name@mcclawis.edu.ph"
                           pattern="[a-zA-Z0-9._%+-]+@.*\.edu\.ph"
                           title="Please enter a valid .edu.ph email address"
                           required
                           autocomplete="email"
                           autofocus>
                    @error('ms365_account')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="form-help">
                        <i class="fas fa-info-circle"></i>
                        We'll send a registration link to your MS365 email address
                    </div>
                </div>

                <button type="submit" class="btn">
                    <i class="fas fa-paper-plane"></i>
                    Send Registration Link
                </button>
            </form>

            <div class="auth-links">
                <a href="{{ route('login') }}">
                    <i class="fas fa-arrow-left"></i>
                    Already have an account? Login
                </a>
            </div>

            <div class="info-section">
                <h3><i class="fas fa-envelope"></i> What happens next?</h3>
                <ol>
                    <li>We'll send a secure registration link to your MS365 email</li>
                    <li>Click the link to access the registration form</li>
                    <li>Complete your profile and set your password</li>
                    <li>Start using MCC News Aggregator!</li>
                </ol>

                <div class="security-note">
                    <i class="fas fa-shield-alt"></i>
                    <strong>Security:</strong> The registration link expires in 30 minutes for your protection.
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.getElementById('swal-success');
            if (el && !el.hasAttribute('data-processed')) {
                // Mark as processed to prevent duplicate alerts
                el.setAttribute('data-processed', 'true');
                
                var msg = el.getAttribute('data-message') || 'Success';
                Swal.fire({
                    icon: 'success',
                    title: 'Registration Link Sent!',
                    text: msg,
                    confirmButtonColor: '#111827',
                    confirmButtonText: 'OK',
                    timer: 5000,
                    timerProgressBar: true
                });
            }
        });
    </script>
</body>
</html>