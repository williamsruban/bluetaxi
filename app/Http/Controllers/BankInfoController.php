<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BankInfo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class BankInfoController extends Controller
{
    // Store bank details
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_name' => 'required|string',
            'ifsc_code' => 'required|string|size:11',
            'account_holder_name' => 'required|string',
            'account_number' => 'required|string|unique:bank_infos,account_number,' . Auth::id() . ',user_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userId = Auth::id();

        // Check if the user's bank info already exists
        $existingBankInfo = BankInfo::where('user_id', $userId)->first();

        if ($existingBankInfo) {
            // Check if all data is identical
            if (
                $existingBankInfo->bank_name === $request->bank_name &&
                $existingBankInfo->ifsc_code === $request->ifsc_code &&
                $existingBankInfo->account_holder_name === $request->account_holder_name &&
                $existingBankInfo->account_number === $request->account_number
            ) {
                return response()->json(['message' => 'Bank information already exists'], 200);
            }

            // Update existing bank info
            $existingBankInfo->update([
                'bank_name' => $request->bank_name,
                'ifsc_code' => $request->ifsc_code,
                'account_holder_name' => $request->account_holder_name,
                'account_number' => $request->account_number,
            ]);

            return response()->json([
                'message' => 'Bank information updated successfully',
                'data' => $existingBankInfo
            ], 200);
        }

        // Create new bank info if it doesn't exist
        $bankInfo = BankInfo::create([
            'user_id' => $userId,
            'bank_name' => $request->bank_name,
            'ifsc_code' => $request->ifsc_code,
            'account_holder_name' => $request->account_holder_name,
            'account_number' => $request->account_number,
        ]);

        return response()->json([
            'message' => 'Bank information saved successfully',
            'data' => $bankInfo
        ], 201);
    }


    // Fetch user bank details
    public function fetch()
    {
        $bankInfo = BankInfo::where('user_id', Auth::id())->first();

        if (!$bankInfo) {
            return response()->json(['message' => 'No bank details found'], 404);
        }

        return response()->json(['message' => 'Bank information Fetch successfully', 'data' => $bankInfo], 200);
    }

    // Update bank details
    public function update(Request $request)
    {
        $bankInfo = BankInfo::where('user_id', Auth::id())->first();

        if (!$bankInfo) {
            return response()->json(['error' => 'Bank details not found'], 404);
        }

        // Check if any field has changed
        $updates = [];

        if ($request->bank_name && $request->bank_name !== $bankInfo->bank_name) {
            $updates['bank_name'] = $request->bank_name;
        }
        if ($request->ifsc_code && $request->ifsc_code !== $bankInfo->ifsc_code) {
            $updates['ifsc_code'] = $request->ifsc_code;
        }
        if ($request->account_holder_name && $request->account_holder_name !== $bankInfo->account_holder_name) {
            $updates['account_holder_name'] = $request->account_holder_name;
        }
        if ($request->account_number && $request->account_number !== $bankInfo->account_number) {
            $updates['account_number'] = $request->account_number;
        }

        if (empty($updates)) {
            return response()->json(['message' => 'No changes detected, bank details are already up-to-date'], 200);
        }

        // Update only changed fields
        $bankInfo->update($updates);

        return response()->json(['message' => 'Bank details updated successfully', 'data' => $bankInfo], 200);
    }
}
