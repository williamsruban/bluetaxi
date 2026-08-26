<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Validator;


class UserProfileController extends Controller
{
    /**
     * Store a new user profile.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_email' => 'nullable|email|unique:user_profiles,new_email',
            'username' => 'required|unique:user_profiles,username',
            'first_name' => 'required',
            'last_name' => 'required',
            'contact_number' => 'nullable|digits:10',
            'address' => 'nullable|string',
            'home_location' => 'nullable|string',
            'old_email' => 'email|exists:users,email', // Ensure old_email exists in users table
        ], [
            'new_email.email' => 'Please enter a valid email address.',
            'new_email.unique' => 'This email is already taken.',
            'username.required' => 'The username field is required.',
            'username.unique' => 'This username is already taken.',
            'first_name.required' => 'The first name field is required.',
            'last_name.required' => 'The last name field is required.',
            'contact_number.digits' => 'The contact number must be exactly 10 digits.',
            'old_email.email' => 'Please enter a valid old email address.',
            'old_email.exists' => 'The provided old email does not exist in our records.',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('id', Auth::user()->getAuthIdentifier())->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // If new_email is empty, use the user's current email
        $newEmail = $request->new_email ?: $user->email;

        $profile = UserProfile::create([
            'user_id' => $user->id,
            'old_email' => $user->email,
            'new_email' => $newEmail,
            'username' => $request->username,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
            'home_location' => $request->home_location,
        ]);

        // Update user's email only if new_email is provided
        if ($request->filled('new_email')) {
            $user->update(['email' => $request->new_email]);
        }

        return response()->json(['message' => 'Profile created successfully', 'data' => $profile], 201);
    }


    /**
     * Fetch user profile by user ID.
     */
    public function fetch()
    {
        $profile = UserProfile::where('user_id', Auth::user()->getAuthIdentifier())->first();

        if (!$profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        return response()->json(['message' => 'Profile Fetch successfully','data' => $profile],200);
    }

    /**
     * Update user profile.
     */
    public function update(Request $request)
    {
        $profile = UserProfile::where('user_id', Auth::id())->first();

        if (!$profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        $user = User::find(Auth::id());
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Prepare updated values only for fields that are changed
        $updates = [];

        if ($request->new_email && $request->new_email !== $profile->new_email) {
            $updates['new_email'] = $request->new_email;
        }

        if ($request->username && $request->username !== $profile->username) {
            $updates['username'] = $request->username;
        }

        if ($request->first_name && $request->first_name !== $profile->first_name) {
            $updates['first_name'] = $request->first_name;
        }

        if ($request->last_name && $request->last_name !== $profile->last_name) {
            $updates['last_name'] = $request->last_name;
        }

        if ($request->contact_number && $request->contact_number !== $profile->contact_number) {
            $updates['contact_number'] = $request->contact_number;
        }

        if ($request->address && $request->address !== $profile->address) {
            $updates['address'] = $request->address;
        }

        if ($request->home_location && $request->home_location !== $profile->home_location) {
            $updates['home_location'] = $request->home_location;
        }

        // Check if there are updates
        if (empty($updates)) {
            return response()->json(['message' => 'No changes detected, profile is already up-to-date'], 200);
        }

        // Update profile with the changed fields
        $profile->update($updates);

        // Update user's email if changed
        if (isset($updates['new_email']) && $user->email !== $request->new_email) {
            $user->update(['email' => $request->new_email]);
        }

        return response()->json(['message' => 'Profile updated successfully', 'data' => $profile], 200);
    }
}

