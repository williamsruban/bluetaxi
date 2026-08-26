<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmergencyContact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EmergencyContactController extends Controller
{
    // Store Emergency Contact
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'phone_number' => 'required|digits:10|unique:emergency_contacts,phone_number',
        ], [
            'name.required' => 'Name is required.',
            'phone_number.required' => 'Phone number is required.',
            'phone_number.digits' => 'Phone number must be 10 digits.',
            'phone_number.unique' => 'This phone number is already in use.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $contact = EmergencyContact::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'phone_number' => $request->phone_number,
        ]);

        return response()->json(['message' => 'Emergency contact added successfully', 'data' => $contact], 201);
    }

    // Fetch Emergency Contacts for Authenticated User
    public function index()
    {
        $contacts = EmergencyContact::where('user_id', Auth::id())->get();
        return response()->json(['message' => 'Emergency contact Fetch successfully','data' => $contacts], 200);
    }

    // Delete an Emergency Contact
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:emergency_contacts,id'
        ], [
            'id.required' => 'ID is required.',

        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $request->validate([
            'id' => 'required|integer|exists:emergency_contacts,id'
        ]);

        $contact = EmergencyContact::where('user_id', Auth::id())->where('id', $request->id)->first();

        if (!$contact) {
            return response()->json(['error' => 'Contact not found'], 404);
        }

        $contact->delete();
        return response()->json(['message' => 'Emergency contact deleted successfully'], 200);
    }
}

