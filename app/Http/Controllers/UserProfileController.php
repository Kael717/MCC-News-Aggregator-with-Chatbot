<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserProfileController extends Controller
{
    /**
     * Update user profile information.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        // Log the incoming data for debugging
        \Log::info('Profile update request data:', $request->all());
        \Log::info('Current user data:', [
            'department' => $user->department,
            'year_level' => $user->year_level
        ]);
        
        // Custom validation for names
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'surname' => 'required|string|max:255',
            'department' => 'required|in:Bachelor of Science in Information Technology,Bachelor of Science in Business Administration,Bachelor of Elementary Education,Bachelor of Science in Hospitality Management,Bachelor of Secondary Education,BSIT,BSBA,BEED,BSHM,BSED',
            'year_level' => 'required|in:1st Year,2nd Year,3rd Year,4th Year,1st year,2nd year,3rd year,4th year',
        ]);

        // Add custom validation rules
        $validator->after(function ($validator) use ($request) {
            // Validate first name (letters and spaces only)
            if ($request->first_name && !preg_match('/^[a-zA-Z\s]+$/', $request->first_name)) {
                $validator->errors()->add('first_name', 'First name can only contain letters and spaces.');
            }
            
            // Validate middle name (letters, spaces, and hyphens in middle)
            if ($request->middle_name && !preg_match('/^[a-zA-Z]+[a-zA-Z\s\-]*[a-zA-Z]$|^[a-zA-Z]$/', $request->middle_name)) {
                $validator->errors()->add('middle_name', 'Middle name can only contain letters, spaces, and hyphens in the middle.');
            }
            
            // Validate surname (letters, spaces, and hyphens in middle)
            if ($request->surname && !preg_match('/^[a-zA-Z]+[a-zA-Z\s\-]*[a-zA-Z]$|^[a-zA-Z]$/', $request->surname)) {
                $validator->errors()->add('surname', 'Last name can only contain letters, spaces, and hyphens in the middle.');
            }
            
            // Check for hyphens at start or end
            if ($request->middle_name && (str_starts_with($request->middle_name, '-') || str_ends_with($request->middle_name, '-'))) {
                $validator->errors()->add('middle_name', 'Middle name cannot start or end with a hyphen.');
            }
            
            if ($request->surname && (str_starts_with($request->surname, '-') || str_ends_with($request->surname, '-'))) {
                $validator->errors()->add('surname', 'Last name cannot start or end with a hyphen.');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user->update([
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'surname' => $request->surname,
                'department' => $request->department,
                'year_level' => $request->year_level,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully!',
                'user' => [
                    'full_name' => $user->full_name,
                    'department' => $user->department,
                    'year_level' => $user->year_level,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload user profile picture.
     */
    public function uploadProfilePicture(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = auth()->user();
            
            // Delete old profile picture if it exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            // Generate unique filename
            $file = $request->file('profile_picture');
            $filename = 'profile_pictures/' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Store the file
            $path = $file->storeAs('profile_pictures', $user->id . '_' . time() . '.' . $file->getClientOriginalExtension(), 'public');
            
            // Update user record
            $user->update(['profile_picture' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'Profile picture uploaded successfully!',
                'profile_picture_url' => asset('storage/' . $path)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading profile picture: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove user profile picture.
     */
    public function removeProfilePicture()
    {
        try {
            $user = auth()->user();
            
            if ($user->profile_picture) {
                // Delete the file from storage
                Storage::disk('public')->delete($user->profile_picture);
                
                // Update user record
                $user->update(['profile_picture' => null]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile picture removed successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing profile picture: ' . $e->getMessage()
            ], 500);
        }
    }
}
