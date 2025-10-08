<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\GmailAuthController;
use App\Http\Controllers\Auth\MS365AuthController;
use App\Http\Controllers\SuperAdminAuthController;
use App\Http\Controllers\DepartmentAdminAuthController;
use App\Http\Controllers\OfficeAdminAuthController;
use App\Traits\SecurityValidationTrait;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Models\User;
class UnifiedAuthController extends Controller
{
    use SecurityValidationTrait;

    /**
     * Validate reCAPTCHA response
     */
    private function validateRecaptcha(Request $request)
    {
        $recaptchaResponse = $request->input('g-recaptcha-response');
        
        if (!$recaptchaResponse) {
            return false;
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret'),
            'response' => $recaptchaResponse,
            'remoteip' => $request->ip(),
        ]);

        $result = $response->json();
        
        return isset($result['success']) && $result['success'] === true;
    }
    
    /**
     * Show the unified login form
     */
    public function showLoginForm(Request $request)
    {
        $loginType = $request->query('type', 'ms365'); // Default to ms365 if no type specified
        
        // Validate the login type
        $validTypes = ['ms365', 'user', 'superadmin', 'department-admin', 'office-admin'];
        if (!in_array($loginType, $validTypes)) {
            $loginType = 'ms365';
        }
        
        // Get locked accounts information
        $lockedAccounts = $this->getLockedAccounts();
        $authenticatedAccounts = $this->getCurrentAuthenticatedAccounts();
        
        return view('auth.unified-login', [
            'title' => 'Login - MCC News Aggregator',
            'preselectedType' => $loginType,
            'lockedAccounts' => $lockedAccounts,
            'authenticatedAccounts' => $authenticatedAccounts
        ]);
    }

    /**
     * Handle unified login based on login type
     */
    public function login(Request $request)
    {
        // Check if specific account is locked out
        if ($this->isLockedOut($request)) {
            $lockoutTime = $this->getLockoutTimeRemaining($request);
            $accountIdentifier = $this->getAccountIdentifier($request);
            return back()->withErrors([
                'account_lockout' => "This account is temporarily locked due to too many failed login attempts. Please try again in {$lockoutTime} minute" . ($lockoutTime != 1 ? 's' : '') . "."
            ])->with('lockout_time', $lockoutTime)
              ->with('locked_account', $accountIdentifier);
        }

        // Enhanced security validation
        $this->validateSecureInput($request);

        $secureRules = $this->getSecureValidationRules();
        $secureMessages = $this->getSecureValidationMessages();

        // Validate basic fields first
        $request->validate([
            'login_type' => 'required|in:user,ms365,superadmin,department-admin,office-admin',
            'ms365_account' => $secureRules['ms365_account'],
            'username' => $secureRules['username'],
            'password' => $secureRules['password'],
        ], $secureMessages);

        $loginType = $request->login_type;

        // Store current auth status before login attempt
        $wasAuthenticated = auth()->check();
        
        // Route to appropriate controller based on login type
        $result = null;
        switch ($loginType) {
            case 'ms365':
                $ms365Controller = new MS365AuthController();
                $result = $ms365Controller->login($request);
                break;

            case 'user':
                $gmailController = new GmailAuthController();
                $result = $gmailController->login($request);
                break;

            case 'superadmin':
                $superAdminController = new SuperAdminAuthController();
                $result = $superAdminController->login($request);
                break;

            case 'department-admin':
                $deptAdminController = new DepartmentAdminAuthController();
                $result = $deptAdminController->login($request);
                break;

            case 'office-admin':
                $officeAdminController = new OfficeAdminAuthController();
                $result = $officeAdminController->login($request);
                break;

            default:
                return back()->withErrors(['login_type' => 'Invalid login type selected.']);
        }

        // Handle login attempt tracking and account switching
        $currentlyAuthenticated = $this->getCurrentAuthenticatedAccounts();
        
        if (auth()->check() && !$wasAuthenticated) {
            // Login successful, clear attempts and store account info
            $this->clearLoginAttempts($request);
            $this->storeAccountSession($request, $loginType);
        } elseif (!auth()->check() && empty($currentlyAuthenticated)) {
            // Login failed and no other accounts logged in, increment attempt counter
            $this->incrementLoginAttempts($request);
            
            // Add remaining attempts info to the error response
            $attemptsLeft = $this->getRemainingAttempts($request);
            if ($attemptsLeft > 0 && $result instanceof \Illuminate\Http\RedirectResponse) {
                $result->with('attempts_left', $attemptsLeft);
            }
        }

        return $result;
    }
    

    public function showSignupForm(Request $request)
    {
        $type = $request->route()->getName() === 'ms365.signup' ? 'ms365' : 'gmail';
        return view('auth.' . $type . '-signup');
    }

    public function sendRegistrationLink(Request $request)
    {
        // Enhanced security validation
        $this->validateSecureInput($request);
        
        $type = $request->route()->getName() === 'ms365.signup.send' ? 'ms365' : 'gmail';
        $secureRules = $this->getSecureValidationRules();
        $secureMessages = $this->getSecureValidationMessages();
        
        $emailField = $type . '_account';
        $request->validate([
            $emailField => array_merge($secureRules[$emailField], [
                'required',
                'unique:users,' . $emailField,
            ]),
        ], $secureMessages);

        $email = $request->{$type . '_account'};

        $registrationUrl = URL::temporarySignedRoute(
            $type . '.register.form',
            now()->addMinutes(30),
            ['email' => $email]
        );

        try {
            Mail::send('emails.' . $type . '-registration', [
                'email' => $email,
                'registrationUrl' => $registrationUrl
            ], function ($message) use ($email) {
                $message->to($email)
                       ->subject('Complete Your Registration - MCC News Aggregator');
            });

            return back()->with('status', 'Registration link sent to your email.');
        } catch (\Exception $e) {
            \Log::error($type . ' registration email failed: ' . $e->getMessage());
            return back()->withErrors('Failed to send registration email. Please try again.');
        }
    }

    public function showRegistrationForm(Request $request)
    {
        $type = $request->route()->getName() === 'ms365.register.form' ? 'ms365' : 'gmail';

        if (!$request->hasValidSignature()) {
            abort(401, 'This link has expired or is invalid.');
        }

        $email = $request->email;

        if (User::where($type . '_account', $email)->exists()) {
            return redirect()->route('login')->withErrors('This email address is already registered.');
        }

        return view('auth.' . $type . '-register', [
            'email' => $email
        ]);
    }

    public function completeRegistration(Request $request)
    {
        // Enhanced security validation
        $this->validateSecureInput($request);
        
        $type = $request->route()->getName() === 'ms365.register.complete' ? 'ms365' : 'gmail';
        $secureRules = $this->getSecureValidationRules();
        $secureMessages = $this->getSecureValidationMessages();

        $emailField = $type . '_account';
        $validator = \Validator::make($request->all(), [
            'email' => array_merge($secureRules[$emailField], [
                'required',
                'unique:users,' . $emailField,
            ]),
            'first_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\pL\' ]+$/u',
                function ($attribute, $value, $fail) {
                    if ($value && $this->containsDangerousPatterns($value)) {
                        $fail('Invalid characters detected in first name.');
                    }
                },
            ],
            'middle_name' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[\pL\' ]+$/u',
                function ($attribute, $value, $fail) {
                    if ($value && $this->containsDangerousPatterns($value)) {
                        $fail('Invalid characters detected in middle name.');
                    }
                },
            ],
            'surname' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\pL\' ]+$/u',
                function ($attribute, $value, $fail) {
                    if ($value && $this->containsDangerousPatterns($value)) {
                        $fail('Invalid characters detected in surname.');
                    }
                },
            ],
            'role' => 'required|in:student,faculty',
            'department' => 'required_if:role,student,faculty|in:Bachelor of Science in Information Technology,Bachelor of Science in Business Administration,Bachelor of Elementary Education,Bachelor of Secondary Education,Bachelor of Science in Hospitality Management',
            'year_level' => 'required_if:role,student|in:1st Year,2nd Year,3rd Year,4th Year',
            'password' => array_merge($secureRules['password'], [
                'required',
                'min:8',
                'confirmed',
            ]),
        ], array_merge($secureMessages, [
            'first_name.regex' => 'First name should only contain letters, spaces, and apostrophes',
            'middle_name.regex' => 'Middle name should only contain letters, spaces, and apostrophes',
            'surname.regex' => 'Surname should only contain letters, spaces, and apostrophes',
            'department.required_if' => 'Department is required for your selected role',
            'year_level.required_if' => 'Year level is required for students',
        ]));

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $userData = [
                $type . '_account' => $request->email,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'surname' => $request->surname,
                'role' => $request->role,
                'department' => $request->department,
                'password' => \Hash::make($request->password),
                'email_verified_at' => now(),
            ];

            // Only set year_level for students
            if ($request->role === 'student') {
                $userData['year_level'] = $request->year_level;
            }

            $user = User::create($userData);

            auth()->login($user);

            return redirect()->route('user.dashboard')->with('status', 'Registration successful! Welcome to MCC News Aggregator.');
        } catch (\Exception $e) {
            \Log::error($type . ' registration failed: ' . $e->getMessage());
            return back()->withErrors('Registration failed. Please try again.')->withInput();
        }
    }

    /**
     * Handle unified logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')
                        ->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show the forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link
     */
    public function sendPasswordResetLink(Request $request)
    {
        // Enhanced security validation
        $this->validateSecureInput($request);
        
        $secureRules = $this->getSecureValidationRules();
        $secureMessages = $this->getSecureValidationMessages();
        
        $request->validate([
            'ms365_account' => array_merge($secureRules['ms365_account'], [
                'required',
            ]),
        ], array_merge($secureMessages, [
            'ms365_account.required' => 'MS365 email address is required',
        ]));

        // Check if user exists
        $user = User::where('ms365_account', $request->ms365_account)->first();
        
        if (!$user) {
            return back()->withErrors([
                'ms365_account' => 'This account is not registered. Please sign up first.'
            ])->with('show_signup', true);
        }

        // Generate reset token
        $token = Str::random(64);
        
        // Store token in password_resets table
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->ms365_account],
            [
                'email' => $request->ms365_account,
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        // Send reset email
        try {
            $resetUrl = route('password.reset', ['token' => $token]) . '?email=' . urlencode($request->ms365_account);
            
            Mail::send('emails.password-reset', [
                'user' => $user,
                'resetUrl' => $resetUrl,
                'token' => $token
            ], function ($message) use ($request) {
                $message->to($request->ms365_account)
                       ->subject('Password Reset Request - MCC News Aggregator');
            });

            return back()->with('status', 'Password reset link has been sent to your MS365 email account.');
        } catch (\Exception $e) {
            \Log::error('Password reset email failed: ' . $e->getMessage());
            return back()->withErrors('Failed to send password reset email. Please try again.');
        }
    }

    /**
     * Show the reset password form
     */
    public function showResetPasswordForm(Request $request, $token)
    {
        $email = $request->query('email');
        
        if (!$email) {
            return redirect()->route('password.request')
                           ->withErrors(['email' => 'Invalid reset link.']);
        }

        // Verify token exists and is valid
        $resetRecord = DB::table('password_resets')
            ->where('email', $email)
            ->first();

        if (!$resetRecord || !Hash::check($token, $resetRecord->token)) {
            return redirect()->route('password.request')
                           ->withErrors(['email' => 'Invalid or expired reset link.']);
        }

        // Check if token is expired (60 minutes)
        if (now()->diffInMinutes($resetRecord->created_at) > 60) {
            DB::table('password_resets')->where('email', $email)->delete();
            return redirect()->route('password.request')
                           ->withErrors(['email' => 'Reset link has expired. Please request a new one.']);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email
        ]);
    }

    /**
     * Reset the password
     */
    public function resetPassword(Request $request)
    {
        // Enhanced security validation
        $this->validateSecureInput($request);
        
        $secureRules = $this->getSecureValidationRules();
        $secureMessages = $this->getSecureValidationMessages();
        
        $request->validate([
            'token' => 'required|string|max:255',
            'email' => array_merge($secureRules['ms365_account'], ['required']),
            'password' => array_merge($secureRules['password'], [
                'required',
                'min:8',
                'confirmed',
            ]),
        ], $secureMessages);

        // Verify token
        $resetRecord = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
            return back()->withErrors(['email' => 'Invalid reset token.']);
        }

        // Check if token is expired
        if (now()->diffInMinutes($resetRecord->created_at) > 60) {
            DB::table('password_resets')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Reset link has expired.']);
        }

        // Find user and update password
        $user = User::where('ms365_account', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Delete the reset token
        DB::table('password_resets')->where('email', $request->email)->delete();

        return redirect()->route('login')
                        ->with('success', 'Your password has been successfully reset. You can now log in with your new password.');
    }

    /**
     * Get the login attempts session key for specific account
     */
    private function getLoginAttemptsKey(Request $request)
    {
        $identifier = $this->getAccountIdentifier($request);
        return 'login_attempts_' . md5($identifier);
    }

    /**
     * Get the lockout session key for specific account
     */
    private function getLockoutKey(Request $request)
    {
        $identifier = $this->getAccountIdentifier($request);
        return 'lockout_time_' . md5($identifier);
    }

    /**
     * Get unique account identifier based on login type
     */
    private function getAccountIdentifier(Request $request)
    {
        $loginType = $request->login_type;
        
        switch ($loginType) {
            case 'ms365':
                return $loginType . '_' . ($request->ms365_account ?? 'unknown');
            case 'user':
                return $loginType . '_' . ($request->gmail_account ?? 'unknown');
            case 'superadmin':
            case 'department-admin':
            case 'office-admin':
                return $loginType . '_' . ($request->username ?? 'unknown');
            default:
                return 'unknown_' . $request->ip();
        }
    }

    /**
     * Increment login attempts
     */
    private function incrementLoginAttempts(Request $request)
    {
        $key = $this->getLoginAttemptsKey($request);
        $attempts = session($key, 0) + 1;
        session([$key => $attempts]);

        // If max attempts reached, set lockout time
        if ($attempts >= 3) {
            $lockoutKey = $this->getLockoutKey($request);
            session([$lockoutKey => now()->addMinutes(1)]);
        }
    }

    /**
     * Clear login attempts
     */
    private function clearLoginAttempts(Request $request)
    {
        $attemptsKey = $this->getLoginAttemptsKey($request);
        $lockoutKey = $this->getLockoutKey($request);
        
        session()->forget([$attemptsKey, $lockoutKey]);
    }

    /**
     * Check if user is locked out
     */
    private function isLockedOut(Request $request)
    {
        $lockoutKey = $this->getLockoutKey($request);
        $lockoutTime = session($lockoutKey);
        
        if (!$lockoutTime) {
            return false;
        }
        
        try {
            // Ensure we have a valid Carbon instance
            $lockoutTime = is_string($lockoutTime) ? \Carbon\Carbon::parse($lockoutTime) : $lockoutTime;
            
            // Skip if not a valid Carbon instance
            if (!$lockoutTime instanceof \Carbon\Carbon) {
                return false;
            }
            
            // Check if lockout time has passed
            if (now()->greaterThan($lockoutTime)) {
                // Lockout expired, clear it
                $this->clearLoginAttempts($request);
                return false;
            }
            
            return true;
        } catch (\Exception $e) {
            // If there's an error parsing the date, assume not locked
            return false;
        }
    }

    /**
     * Get remaining lockout time in minutes
     */
    private function getLockoutTimeRemaining(Request $request)
    {
        $lockoutKey = $this->getLockoutKey($request);
        $lockoutTime = session($lockoutKey);
        
        if (!$lockoutTime) {
            return 0;
        }
        
        try {
            // Ensure we have a valid Carbon instance
            $lockoutTime = is_string($lockoutTime) ? \Carbon\Carbon::parse($lockoutTime) : $lockoutTime;
            
            // Skip if not a valid Carbon instance
            if (!$lockoutTime instanceof \Carbon\Carbon) {
                return 0;
            }
            
            $remaining = now()->diffInMinutes($lockoutTime, false);
            return max(0, $remaining);
        } catch (\Exception $e) {
            // If there's an error parsing the date, return 0
            return 0;
        }
    }

    /**
     * Get remaining login attempts
     */
    private function getRemainingAttempts(Request $request)
    {
        $key = $this->getLoginAttemptsKey($request);
        $attempts = session($key, 0);
        return max(0, 3 - $attempts);
    }

    /**
     * Store account session information
     */
    private function storeAccountSession(Request $request, $loginType)
    {
        $accounts = session('authenticated_accounts', []);
        
        $accountInfo = [
            'type' => $loginType,
            'user_id' => auth()->id(),
            'name' => auth()->user()->name ?? auth()->user()->username ?? 'Unknown',
            'email' => $this->getUserEmail(auth()->user(), $loginType),
            'logged_in_at' => now()->toDateTimeString(),
        ];
        
        // Remove existing account of same type to prevent duplicates
        $accounts = array_filter($accounts, function($account) use ($loginType) {
            return $account['type'] !== $loginType;
        });
        
        $accounts[] = $accountInfo;
        session(['authenticated_accounts' => $accounts]);
    }

    /**
     * Get current authenticated accounts
     */
    private function getCurrentAuthenticatedAccounts()
    {
        return session('authenticated_accounts', []);
    }

    /**
     * Get user email based on account type
     */
    private function getUserEmail($user, $loginType)
    {
        if (!$user) return 'Unknown';
        
        switch ($loginType) {
            case 'ms365':
                return $user->ms365_account ?? $user->email ?? 'Unknown';
            case 'user':
                return $user->gmail_account ?? $user->email ?? 'Unknown';
            case 'superadmin':
            case 'department-admin':
            case 'office-admin':
                return $user->username ?? 'Unknown';
            default:
                return $user->email ?? $user->username ?? 'Unknown';
        }
    }

    /**
     * Switch to a different account
     */
    public function switchAccount(Request $request)
    {
        $request->validate([
            'account_type' => 'required|string',
            'user_id' => 'required|integer',
        ]);

        $accounts = $this->getCurrentAuthenticatedAccounts();
        $targetAccount = collect($accounts)->firstWhere('type', $request->account_type);

        if (!$targetAccount) {
            return back()->withErrors(['account' => 'Account not found or session expired.']);
        }

        // Switch authentication context based on account type
        switch ($request->account_type) {
            case 'ms365':
            case 'user':
                $user = User::find($request->user_id);
                if ($user) {
                    auth()->login($user);
                    return redirect()->route('user.dashboard');
                }
                break;
            
            case 'superadmin':
            case 'department-admin':
            case 'office-admin':
                $admin = Admin::find($request->user_id);
                if ($admin) {
                    auth('admin')->login($admin);
                    return redirect()->route($request->account_type . '.dashboard');
                }
                break;
        }

        return back()->withErrors(['account' => 'Unable to switch to the selected account.']);
    }

    /**
     * Remove an account from the session
     */
    public function removeAccount(Request $request)
    {
        $request->validate([
            'account_type' => 'required|string',
        ]);

        $accounts = $this->getCurrentAuthenticatedAccounts();
        $accounts = array_filter($accounts, function($account) use ($request) {
            return $account['type'] !== $request->account_type;
        });

        session(['authenticated_accounts' => array_values($accounts)]);

        // If removing current account, logout
        if (auth()->check()) {
            $currentType = $this->getCurrentAccountType();
            if ($currentType === $request->account_type) {
                auth()->logout();
                
                // If there are other accounts, switch to the first one
                if (!empty($accounts)) {
                    $firstAccount = reset($accounts);
                    return $this->switchAccount(new Request([
                        'account_type' => $firstAccount['type'],
                        'user_id' => $firstAccount['user_id']
                    ]));
                }
            }
        }

        return back()->with('success', 'Account removed successfully.');
    }

    /**
     * Get current account type
     */
    private function getCurrentAccountType()
    {
        if (auth('admin')->check()) {
            $admin = auth('admin')->user();
            return $admin->role === 'superadmin' ? 'superadmin' : 
                   ($admin->role === 'department_admin' ? 'department-admin' : 'office-admin');
        } elseif (auth()->check()) {
            $user = auth()->user();
            return $user->ms365_account ? 'ms365' : 'user';
        }
        return null;
    }

    /**
     * Get all locked accounts with their lockout information
     */
    private function getLockedAccounts()
    {
        $lockedAccounts = [];
        $sessionData = session()->all();
        
        foreach ($sessionData as $key => $value) {
            if (strpos($key, 'lockout_time_') === 0) {
                try {
                    // Ensure we have a valid Carbon instance
                    $lockoutTime = is_string($value) ? \Carbon\Carbon::parse($value) : $value;
                    
                    // Skip if not a valid Carbon instance
                    if (!$lockoutTime instanceof \Carbon\Carbon) {
                        continue;
                    }
                    
                    if (now()->lessThan($lockoutTime)) {
                        // Find corresponding attempts key
                        $attemptsKey = str_replace('lockout_time_', 'login_attempts_', $key);
                        $attempts = session($attemptsKey, 0);
                        
                        $lockedAccounts[] = [
                            'key' => $key,
                            'lockout_time' => $lockoutTime,
                            'attempts' => $attempts,
                            'remaining_minutes' => now()->diffInMinutes($lockoutTime, false)
                        ];
                    }
                } catch (\Exception $e) {
                    // Skip invalid lockout entries
                    continue;
                }
            }
        }
        
        return $lockedAccounts;
    }

    /**
     * Check if specific account identifier is locked
     */
    public function isAccountLocked($accountIdentifier)
    {
        $key = 'lockout_time_' . md5($accountIdentifier);
        $lockoutTime = session($key);
        
        if (!$lockoutTime) {
            return false;
        }
        
        try {
            // Ensure we have a valid Carbon instance
            $lockoutTime = is_string($lockoutTime) ? \Carbon\Carbon::parse($lockoutTime) : $lockoutTime;
            
            // Skip if not a valid Carbon instance
            if (!$lockoutTime instanceof \Carbon\Carbon) {
                return false;
            }
            
            if (now()->greaterThan($lockoutTime)) {
                // Lockout expired, clear it
                $attemptsKey = 'login_attempts_' . md5($accountIdentifier);
                session()->forget([$key, $attemptsKey]);
                return false;
            }
            
            return true;
        } catch (\Exception $e) {
            // If there's an error parsing the date, assume not locked
            return false;
        }
    }

}