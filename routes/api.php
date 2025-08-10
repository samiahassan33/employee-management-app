<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\DepartmentController;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::get('user', [AuthController::class, 'user']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
    });

    // Users management (Admin only)
    Route::middleware('permission:users.view')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::post('users/{user}/assign-role', [UserController::class, 'assignRole'])
            ->middleware('permission:users.edit');
        Route::get('users/{user}/permissions', [UserController::class, 'getUserPermissions']);
    });

    // Employees management
    Route::middleware('permission:employees.view')->group(function () {
        Route::get('employees', [EmployeeController::class, 'index']);
        Route::get('employees/{employee}', [EmployeeController::class, 'show']);
        Route::get('employees/search/{term}', [EmployeeController::class, 'search']);
        Route::get('employees/department/{department}', [EmployeeController::class, 'getByDepartment']);
    });

    Route::middleware('permission:employees.create')->group(function () {
        Route::post('employees', [EmployeeController::class, 'store']);
    });

    Route::middleware('permission:employees.edit')->group(function () {
        Route::put('employees/{employee}', [EmployeeController::class, 'update']);
    });

    Route::middleware('permission:employees.delete')->group(function () {
        Route::delete('employees/{employee}', [EmployeeController::class, 'destroy']);
        Route::post('employees/{employee}/restore', [EmployeeController::class, 'restore']);
    });

    // Departments management
    Route::middleware('permission:departments.view')->group(function () {
        Route::get('departments', [DepartmentController::class, 'index']);
        Route::get('departments/{department}', [DepartmentController::class, 'show']);
        Route::get('departments/with-employees', [DepartmentController::class, 'withEmployees']);
    });

    Route::middleware('permission:departments.create')->group(function () {
        Route::post('departments', [DepartmentController::class, 'store']);
    });

    Route::middleware('permission:departments.edit')->group(function () {
        Route::put('departments/{department}', [DepartmentController::class, 'update']);
    });

    Route::middleware('permission:departments.delete')->group(function () {
        Route::delete('departments/{department}', [DepartmentController::class, 'destroy']);
    });

    // Dashboard & Statistics
    Route::get('dashboard/stats', function () {
        return response()->json([
            'employees_count' => \App\Models\Employee::count(),
            'departments_count' => \App\Models\Department::count(),
            'users_count' => \App\Models\User::count(),
            'recent_joinings' => \App\Models\Employee::where('joining_date', '>=', now()->subDays(30))->count(),
        ]);
    })->middleware('permission:dashboard.view');

    // Export routes
    Route::get('employees/export/csv', [EmployeeController::class, 'exportCsv'])
        ->middleware('permission:employees.export');
    Route::get('employees/export/pdf', [EmployeeController::class, 'exportPdf'])
        ->middleware('permission:employees.export');
});

// Fallback route
Route::fallback(function () {
    return response()->json([
        'message' => 'API endpoint not found'
    ], 404);
});

