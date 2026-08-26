<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed'
        ], [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a valid string.',
            'name.max' => 'The name cannot exceed 255 characters.',

            'email.required' => 'The email field is required.',
            'email.string' => 'The email must be a valid string.',
            'email.email' => 'Enter a valid email address.',
            'email.max' => 'The email cannot exceed 255 characters.',
            'email.unique' => 'This email is already registered.',

            'password.required' => 'The password field is required.',
            'password.string' => 'The password must be a valid string.',
            'password.min' => 'The password must be at least 6 characters.',
            'password.confirmed' => 'Password confirmation does not match.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }

    // User Login
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => 'The email field is required.',
            'email.email' => 'Enter a valid email address.',
            'password.required' => 'The password field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email not found'], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Incorrect password'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    // User Logout
    public function logout(Request $request)
    {

        User::where('id', Auth::id())->update(['status' => 'offline']);
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }
    public function changeStatus(Request $request): JsonResponse
    {

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:online,offline'
        ], [
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be either online or offline.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        $user = Auth::user(); // Get the authenticated user

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $user->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Status updated successfully',
            'user' => $user
        ]);
    }
    public function changePassword(Request $request)
    {
        // Validate input fields
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:6|confirmed', // Ensure new_password_confirmation field is sent
        ], [
            'old_password.required' => 'The old password is required.',
            'new_password.required' => 'The new password is required.',
            'new_password.min' => 'The new password must be at least 6 characters.',
            'new_password.confirmed' => 'The new password confirmation does not match.',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Get the authenticated user
        $user = Auth::user();

        // Verify old password
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['error' => 'Old password is incorrect.'], 400);
        }

        // Update the password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json(['message' => 'Password changed successfully.'], 200);
    }
    public function deleteAccount(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Soft delete the user
        $user->delete();

        // Revoke the user's authentication tokens (log out)
        $user->tokens()->delete();

        return response()->json(['message' => 'Account deleted successfully and logged out.'], 200);
    }
}

