<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function index(Request $request)
    {
        $users = $this->userService->getPaginated($request->all());
        
        return UserResource::collection($users);
    }

    public function show(User $user)
    {
        return UserResource::make($user->load('roles', 'permissions'));
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $user = $this->userService->createWithRole($request->validated());
            
            return UserResource::make($user);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $updatedUser = $this->userService->update($user->id, $request->validated());
            
            return UserResource::make($updatedUser);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(User $user)
    {
        try {
            // Prevent self-deletion
            if ($user->id === auth()->id()) {
                return response()->json([
                    'message' => 'You cannot delete your own account'
                ], 422);
            }

            $this->userService->delete($user->id);
            
            return response()->json([
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|string|in:admin,manager,employee'
        ]);

        try {
            $updatedUser = $this->userService->assignRole($user, $request->role);
            
            return UserResource::make($updatedUser);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to assign role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUserPermissions(User $user)
    {
        return response()->json([
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'roles' => $user->getRoleNames(),
        ]);
    }

}
