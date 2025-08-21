<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminProfileController extends Controller
{
    public function getProfile()
    {
        return response()->json([
            'name' => 'Admin John Doe',
            'email' => 'admin@example.com',
            'role' => 'Administrator',
            'phone' => '+91 9876543210',
            'address' => 'Mumbai, India',
            'lastLogin' => now()->subDays(1)->toDateTimeString(),
            'profilePic' => url('/images/admin-avatar.png'),
            'bio' => 'Managing the platform with excellence for 2+ years.'
        ]);
    }

    public function updateProfile(Request $request)
    {
        return response()->json([
            'message' => 'Profile updated successfully',
            'updated_data' => $request->all()
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'currentPassword' => 'required|string',
            'newPassword' => 'required|string|min:6|confirmed'
        ]);

        return response()->json(['message' => 'Password updated successfully']);
    }

    public function uploadProfilePic(Request $request)
    {
        $request->validate([
            'profilePic' => 'required|image|max:2048'
        ]);

        $path = $request->file('profilePic')->store('profile-pics', 'public');

        return response()->json([
            'message' => 'Profile picture uploaded successfully',
            'url' => Storage::url($path)
        ]);
    }
}
