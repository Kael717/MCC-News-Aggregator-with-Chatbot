<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Traits\SecurityValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DepartmentAdminAuthController extends Controller
{
    use SecurityValidationTrait;
    /**
     * Show the department admin login form
     */
    public function showLoginForm()
    {
        return view('department-admin.auth.login');
    }

    /**
     * Handle department admin login
     */
    public function login(Request $request)
    {
        // Enhanced security validation
        $this->validateSecureInput($request);

        $secureRules = $this->getSecureValidationRules();
        $secureMessages = $this->getSecureValidationMessages();

        $request->validate([
            'ms365_account' => array_merge($secureRules['ms365_account'], ['required']),
            'password' => array_merge($secureRules['password'], ['required']),
        ], $secureMessages);

        // Attempt to authenticate with admin guard using ms365_account as username
        if (Auth::guard('admin')->attempt(['username' => $request->ms365_account, 'password' => $request->password])) {
            $admin = Auth::guard('admin')->user();

            // Check if the user is specifically a department admin
            if (!$admin->isDepartmentAdmin()) {
                Auth::guard('admin')->logout();
                
                // Provide specific error messages based on admin type
                if ($admin->isSuperAdmin()) {
                    return back()->withErrors(['ms365_account' => 'Super admins should use the super admin login.']);
                } else {
                    return back()->withErrors(['ms365_account' => 'You do not have department admin privileges.']);
                }
            }

            // Successful department admin login
            $request->session()->regenerate();

            return redirect()->route('department-admin.dashboard')
                           ->with('login_success', true);
        }

        // Authentication failed
        return back()->withErrors(['ms365_account' => 'Invalid credentials. Please check your MS365 account and password.'])
                    ->withInput($request->only('ms365_account'));
    }

    /**
     * Handle department admin logout
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
