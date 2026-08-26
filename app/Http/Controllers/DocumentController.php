<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    // Upload documents linked to the authenticated user
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rc_book' => ['nullable', 'file', 'mimes:pdf,jpg,png', 'max:2048'],
            'road_tax' => ['nullable', 'file', 'mimes:pdf,jpg,png', 'max:2048'],
            'driving_licence' => ['nullable', 'file', 'mimes:pdf,jpg,png', 'max:2048'],
            'permit' => ['nullable', 'file', 'mimes:pdf,jpg,png', 'max:2048'],
            'drivers_batch' => ['nullable', 'file', 'mimes:pdf,jpg,png', 'max:2048']
        ], [
            'file.mimes' => 'Only PDF, JPG, and PNG files are allowed.',
            'file.max' => 'The file size must not exceed 2MB.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->messages()
            ], 422);
        }

        $userId = Auth::id();
        $document = Document::where('user_id', $userId)->first();

        $data = ['user_id' => $userId];
        $updatedFiles = [];

        foreach (['rc_book', 'road_tax', 'driving_licence', 'permit', 'drivers_batch'] as $field) {
            if ($request->hasFile($field)) {
                // Delete old file if it exists
                if ($document && $document->$field) {
                    Storage::disk('public')->delete($document->$field);
                }

                // Store new file
                $filePath = $request->file($field)->store('documents', 'public');
                $data[$field] = $filePath;
                $updatedFiles[$field] = asset("storage/{$filePath}");
            }
        }

        // Update or Create document record
        $document = Document::updateOrCreate(['user_id' => $userId], $data);

        return response()->json([
            'message' => 'Documents uploaded successfully',
            'updated_fields' => $updatedFiles,
            'document' => $document
        ], 200);
    }

    // Fetch a All document
    public function show()
    {
        $document = Document::where('user_id', Auth::id())->firstOrFail();
        return response()->json(['message' => 'Documents Fetched successfully', 'data' => $document], 200);
    }

    // Download a specific document file
    public function download(Request $request)
    {
        // Validate request input
        $request->validate([
            'id' => 'required|integer|exists:documents,id',
            'columnName' => 'required|string|in:rc_book,road_tax,driving_licence,permit,drivers_batch'
        ]);

        $columnName = $request->columnName;

        // Fetch the document for the authenticated user
        $document = Document::where('id', $request->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $filePath = $document->$columnName;

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // Download the file
        return response()->download(storage_path("app/public/{$filePath}"));
    }
    public function fetchSelectedDocument(Request $request)
    {
        // Validate request input
        $request->validate([
            'columnName' => 'required|string|in:rc_book,road_tax,driving_licence,permit,drivers_batch'
        ]);

        $columnName = $request->columnName;
        $userId = Auth::id(); // Get authenticated user ID

        // Fetch the document for the authenticated user
        $document = Document::where('user_id', $userId)->first();

        if (!$document || !$document->$columnName) {
            return response()->json(['error' => 'Document not found'], 404);
        }

        return response()->json([
            'message' => 'Document fetched successfully',
            'data' => [
                'id' => $document->id,
                'user_id' => $userId,
                'file_name' => $columnName,
                'url' => asset("storage/{$document->$columnName}")
            ]
        ], 200);
    }


}

