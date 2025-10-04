<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('user.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'ms365_account' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt(['ms365_account' => $request->ms365_account, 'password' => $request->password], $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('user.dashboard'));
        }

        return back()->withErrors(['ms365_account' => 'Invalid credentials'])->withInput($request->except('password'));
    }

    public function showRegisterForm()
    {
        return view('user.auth.register');
    }

    public function register(Request $request)
    {
        $rules = [
            'first_name' => 'required|string|max:255|regex:/^[\pL\' ]+$/u',
            'middle_name' => 'nullable|string|max:255|regex:/^[\pL\' ]+$/u',
            'surname' => 'required|string|max:255|regex:/^[\pL\' ]+$/u',
            'ms365_account' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|string|same:password',
            'role' => 'required|in:student,faculty',
        ];

        if ($request->role === 'student') {
            $rules['department'] = 'required|in:Bachelor of Science in Information Technology,Bachelor of Science in Business Administration,Bachelor of Elementary Education,Bachelor of Secondary Education,Bachelor of Science in Hospitality Management';
            $rules['year_level'] = 'required|in:1st Year,2nd Year,3rd Year,4th Year';
        }

        $request->validate($rules);

        User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'surname' => $request->surname,
            'ms365_account' => $request->ms365_account,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'department' => $request->role === 'student' ? $request->department : null,
            'year_level' => $request->role === 'student' ? $request->year_level : null,
        ]);

        return redirect()->route('login')->with('success', 'Registration successful! Please login.');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')
                        ->with('success', 'You have been logged out successfully.');
    }

    public function updateSettings(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'first_name' => 'required|string|max:255|regex:/^[\pL\' ]+$/u',
            'middle_name' => 'nullable|string|max:255|regex:/^[\pL\' ]+$/u',
            'surname' => 'required|string|max:255|regex:/^[\pL\' ]+$/u',
            'ms365_account' => 'required|email|unique:users,ms365_account,' . $user->id,
        ];

        // Add student-specific validation
        if ($user->role === 'student') {
            $rules['department'] = 'required|in:Bachelor of Science in Information Technology,Bachelor of Science in Business Administration,Bachelor of Elementary Education,Bachelor of Secondary Education,Bachelor of Science in Hospitality Management';
            $rules['year_level'] = 'required|in:1st Year,2nd Year,3rd Year,4th Year';
        }

        // Only validate password if provided
        if ($request->filled('new_password')) {
            $rules['current_password'] = 'required';
            $rules['new_password'] = 'required|string|min:6';
            $rules['new_password_confirmation'] = 'required|string|same:new_password';
        }

        $request->validate($rules);
        
        // Verify current password if changing password
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        // Update profile information
        $user->first_name = $request->first_name;
        $user->middle_name = $request->middle_name;
        $user->surname = $request->surname;
        $user->ms365_account = $request->ms365_account;

        // Update student-specific fields
        if ($user->role === 'student') {
            $user->department = $request->department;
            $user->year_level = $request->year_level;
        }

        $user->save();
        
        return back()->with('success', 'Settings updated successfully!');
    }
}
