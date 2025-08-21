<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SignupController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required|in:user,admin' // Only allow user or admin
        ]);

        $userId = DB::table('users')->insertGetId([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => [
                'id' => $userId,
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role
            ]
        ], 201);
    }
}
