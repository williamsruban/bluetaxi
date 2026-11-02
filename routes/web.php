<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/login', function (Request $request) {
//     if (!$request->bearerToken()) {
//         return response()->json(['message' => 'Token is missing, please login'], 401);
//     }
// })->name('login');

Route::get('/', function () {
    return response()->json([
        'success' => true,
        'message' => 'BlueTaxi API is running successfully',
    ], 200);
});
