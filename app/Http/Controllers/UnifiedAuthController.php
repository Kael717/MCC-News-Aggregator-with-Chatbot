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
        
        return view('auth.unified-login', [
            'title' => 'Login - MCC News Aggregator',
            'preselectedType' => $loginType
        ]);
    }

    /**
     * Handle unified login based on login type
     */
    public function login(Request $request)
    {
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


}