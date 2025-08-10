<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// SPA Routes - All routes handled by Vue Router
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '^(?!api).*$');

// Development routes (only in development)
if (app()->environment('local')) {
    Route::get('/test-permissions', function () {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Not authenticated'], 401);
        }

        return response()->json([
            'user' => $user->load('roles', 'permissions'),
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ]);
    })->middleware('auth:sanctum');
}
