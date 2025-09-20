<?php

use Illuminate\Support\Facades\Route;

// Health check endpoint for Railway
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'app' => config('app.name'),
        'version' => '1.0.0',
        'environment' => config('app.env'),
        'debug' => config('app.debug')
    ]);
});

// Simple test route
Route::get('/', function () {
    return response()->json([
        'message' => 'Palsa POS System is running!',
        'status' => 'success',
        'timestamp' => now()
    ]);
});

// Basic welcome page
Route::get('/welcome', function () {
    return view('welcome');
});

// Test database connection
Route::get('/test-db', function () {
    try {
        \DB::connection()->getPdo();
        return response()->json([
            'database' => 'connected',
            'status' => 'ok'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'database' => 'failed',
            'error' => $e->getMessage(),
            'status' => 'error'
        ], 500);
    }
});











