<?php

use App\Http\Controllers\StudentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Test routes
Route::get('/ping', function () {
    return response()->json(['message' => 'The API works!']);
});

Route::get('/hello', function () {
    return response()->json([
        'name' => 'Himali',
        'role' => 'Student',
    ]);
});

// Creates all 5 student routes (index, store, show, update, destroy)
Route::apiResource('students', StudentController::class);