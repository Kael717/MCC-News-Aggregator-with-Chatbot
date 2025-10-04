<?php

namespace App\Http\Controllers;

use App\Traits\SecurityValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfficeAdminAuthController extends Controller
{
    use SecurityValidationTrait;
    /**
     * Show the office admin login form
     */
    public function showLoginForm()
    {
        return view('office-admin.auth.login');
    }

    /**
     * Handle office admin login
     */
    public function login(Request $request)
    {
        // Enhanced security validation
        $this->validateSecureInput($request);

        $secureRules = $this->getSecureValidationRules();
        $secureMessages = $this->getSecureValidationMessages();

        $request->validate([
            'username' => array_merge($secureRules['username'], ['required']),
            'password' => array_merge($secureRules['password'], ['required']),
        ], $secureMessages);

        // Attempt to authenticate with admin guard
        if (Auth::guard('admin')->attempt($request->only('username', 'password'))) {
            $admin = Auth::guard('admin')->user();

            // Check if the user is specifically an office admin
            if (!$admin->isOfficeAdmin()) {
                Auth::guard('admin')->logout();
                
                // Provide specific error messages based on admin type
                if ($admin->isSuperAdmin()) {
                    return back()->withErrors(['username' => 'Super admins should use the super admin login.']);
                } elseif ($admin->isDepartmentAdmin()) {
                    return back()->withErrors(['username' => 'Department admins should use the department admin login.']);
                } else {
                    return back()->withErrors(['username' => 'You do not have office admin privileges.']);
                }
            }

            // Successful office admin login
            $request->session()->regenerate();

            return redirect()->route('office-admin.dashboard')
                           ->with('login_success', true);
        }

        // Authentication failed
        return back()->withErrors(['username' => 'Invalid credentials. Please check your username and password.'])
                    ->withInput($request->only('username'));
    }

    /**
     * Handle office admin logout
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
                        ->with('success', 'You have been logged out successfully.');
    }
}
