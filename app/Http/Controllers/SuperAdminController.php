<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SuperAdminController extends Controller
{
    /**
     * Display a listing of admins (excluding office admins)
     */
    public function index()
    {
        // Get all admins except office admins (they have their own management section)
        $admins = Admin::whereIn('role', ['superadmin', 'department_admin', 'admin'])
                      ->latest()
                      ->get();
        return view('superadmin.admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new admin
     */
    public function create()
    {
        return view('superadmin.admins.create');
    }

    /**
     * Store a newly created department admin
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:admins|max:255',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|string|same:password',
            'role' => 'required|in:department_admin',
            'department' => 'required|in:BSIT,BSBA,EDUC,BSED,BSHM',
        ]);

        // Check if department admin already exists for this department
        $existingDeptAdmin = Admin::where('role', 'department_admin')
                                 ->where('department', $request->department)
                                 ->first();

        if ($existingDeptAdmin) {
            return back()->withErrors(['department' => 'A department admin already exists for ' . $request->department . ' department.'])
                        ->withInput();
        }

        Admin::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'department_admin',
            'department' => $request->department,
        ]);

        return redirect()->route('superadmin.admins.index')
                        ->with('success', 'Department admin created successfully!');
    }

    /**
     * Display the specified admin
     */
    public function show(Admin $admin)
    {
        return view('superadmin.admins.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified admin
     */
    public function edit(Admin $admin)
    {
        return view('superadmin.admins.edit', compact('admin'));
    }

    /**
     * Update the specified admin
     */
    public function update(Request $request, Admin $admin)
    {
        $rules = [
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('admins')->ignore($admin->id),
            ],
            'password' => 'nullable|string|min:6',
            'password_confirmation' => 'nullable|string|same:password',
        ];

        // If password is provided, make confirmation required
        if ($request->filled('password')) {
            $rules['password_confirmation'] = 'required|string|same:password';
        }

        $request->validate($rules);

        $updateData = [
            'username' => $request->username,
        ];

        // Only update password if it's provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $admin->update($updateData);

        return redirect()->route('superadmin.admins.index')
                        ->with('success', 'Admin updated successfully!');
    }

    /**
     * Remove the specified admin
     */
    public function destroy(Admin $admin)
    {
        // Prevent deletion of the last super admin
        if ($admin->isSuperAdmin() && Admin::superAdmins()->count() <= 1) {
            return redirect()->route('superadmin.admins.index')
                            ->with('error', 'Cannot delete the last super admin!');
        }

        // Prevent self-deletion
        if ($admin->id === auth('admin')->id()) {
            return redirect()->route('superadmin.admins.index')
                            ->with('error', 'You cannot delete your own account!');
        }

        $admin->delete();

        return redirect()->route('superadmin.admins.index')
                        ->with('success', 'Admin deleted successfully!');
    }

    /**
     * Show the form for creating a department admin
     */
    public function createDepartmentAdmin()
    {
        return view('superadmin.department-admins.create');
    }

    /**
     * Store a newly created department admin
     */
    public function storeDepartmentAdmin(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:admins|max:255',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|string|same:password',
            'department' => 'required|in:BSIT,BSBA,EDUC,BSED,BSHM',
        ]);

        // Check if department admin already exists for this department
        $existingDeptAdmin = Admin::where('role', 'department_admin')
                                 ->where('department', $request->department)
                                 ->first();

        if ($existingDeptAdmin) {
            return back()->withErrors(['department' => 'A department admin already exists for ' . $request->department]);
        }

        Admin::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'department_admin',
            'department' => $request->department,
        ]);

        return redirect()->route('superadmin.admins.index')
                        ->with('success', 'Department admin created successfully!');
    }

    /**
     * Display department admins
     */
    public function departmentAdmins()
    {
        $departmentAdmins = Admin::departmentAdmins()->latest()->get();
        return view('superadmin.department-admins.index', compact('departmentAdmins'));
    }
}
