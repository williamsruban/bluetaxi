<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\BankInfoController;
use App\Http\Controllers\EmergencyContactController;
use App\Http\Controllers\DocumentController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware( 'auth:sanctum')->post('/change-status', [AuthController::class, 'changeStatus']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/menu-list', [MenuController::class, 'index']);
    Route::post('/menu-create', [MenuController::class, 'store']);
    Route::post('/profile/store', [UserProfileController::class, 'store']);
    Route::get('/profile/fetch', [UserProfileController::class, 'fetch']);
    Route::put('/profile/update', [UserProfileController::class, 'update']);
    Route::post('/bank-info/store', [BankInfoController::class, 'store']);
    Route::get('/bank-info/fetch', [BankInfoController::class, 'fetch']);
    Route::put('/bank-info/update', [BankInfoController::class, 'update']);
    Route::post('/emergency-contacts/store', [EmergencyContactController::class, 'store']);
    Route::get('/emergency-contacts/list', [EmergencyContactController::class, 'index']);
    Route::delete('/emergency-contacts/delete', [EmergencyContactController::class, 'destroy']);
    Route::post('/documents/save', [DocumentController::class, 'store']);
    Route::get('/documents/fetch-all', [DocumentController::class, 'show']);
    Route::get('/documents/download', [DocumentController::class, 'download']);
    Route::get('/documents/show', [DocumentController::class, 'fetchSelectedDocument']);

});
Route::post('/settings/change-password', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');
Route::delete('/settings/delete-account', [AuthController::class, 'deleteAccount'])->middleware('auth:sanctum');


