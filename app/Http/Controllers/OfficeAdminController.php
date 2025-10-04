<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OfficeAdminController extends Controller
{
    /**
     * Display a listing of office admins
     */
    public function index()
    {
        $officeAdmins = Admin::officeAdmins()->latest()->get();
        return view('superadmin.office-admins.index', compact('officeAdmins'));
    }

    /**
     * Show the form for creating a new office admin
     */
    public function create()
    {
        return view('superadmin.office-admins.create');
    }

    /**
     * Store a newly created office admin
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:admins|max:255',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|string|same:password',
            'office' => 'required|in:NSTP,SSC,GUIDANCE,REGISTRAR,CLINIC',
        ]);

        // Check if office admin already exists for this office
        $existingOfficeAdmin = Admin::where('role', 'office_admin')
                                   ->where('office', $request->office)
                                   ->first();

        if ($existingOfficeAdmin) {
            return back()->withErrors(['office' => 'An office admin already exists for ' . $request->office . ' office.'])
                        ->withInput();
        }

        Admin::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'office_admin',
            'office' => $request->office,
        ]);

        return redirect()->route('superadmin.office-admins.index')
                        ->with('success', 'Office admin created successfully!');
    }

    /**
     * Display the specified office admin
     */
    public function show(Admin $officeAdmin)
    {
        return view('superadmin.office-admins.show', compact('officeAdmin'));
    }

    /**
     * Show the form for editing the specified office admin
     */
    public function edit(Admin $officeAdmin)
    {
        return view('superadmin.office-admins.edit', compact('officeAdmin'));
    }

    /**
     * Update the specified office admin
     */
    public function update(Request $request, Admin $officeAdmin)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:admins,username,' . $officeAdmin->id,
            'password' => 'nullable|string|min:6',
            'password_confirmation' => 'nullable|string|same:password',
            'office' => 'required|in:NSTP,SSC,GUIDANCE,REGISTRAR,CLINIC',
        ]);

        // Check if office admin already exists for this office (excluding current admin)
        $existingOfficeAdmin = Admin::where('role', 'office_admin')
                                   ->where('office', $request->office)
                                   ->where('id', '!=', $officeAdmin->id)
                                   ->first();

        if ($existingOfficeAdmin) {
            return back()->withErrors(['office' => 'An office admin already exists for ' . $request->office . ' office.'])
                        ->withInput();
        }

        $updateData = [
            'username' => $request->username,
            'office' => $request->office,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $officeAdmin->update($updateData);

        return redirect()->route('superadmin.office-admins.index')
                        ->with('success', 'Office admin updated successfully!');
    }

    /**
     * Remove the specified office admin
     */
    public function destroy(Admin $officeAdmin)
    {
        $officeAdmin->delete();

        return redirect()->route('superadmin.office-admins.index')
                        ->with('success', 'Office admin deleted successfully!');
    }
}
