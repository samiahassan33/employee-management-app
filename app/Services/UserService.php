<?php
namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService
{
    public function __construct(private UserRepositoryInterface $userRepository) {}

    public function getPaginated(array $filters = [], int $perPage = 15)
    {
        $query = User::with('roles')->newQuery();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($filters['role'])) {
            $query->role($filters['role']);
        }

        $sortBy = $filters['sort'] ?? 'created_at';
        $sortOrder = $filters['order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    public function createWithRole(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['password'] = Hash::make($data['password']);
            $role = $data['role'];
            unset($data['role']);

            $user = $this->userRepository->create($data);
            $user->assignRole($role);
            
            Log::info('User created with role', [
                'user_id' => $user->id,
                'role' => $role,
                'created_by' => auth()->id(),
            ]);

            return $user->load('roles');
        });
    }

    public function update(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $user = User::findOrFail($id);
            
            // Handle password update
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            // Handle role update
            if (isset($data['role'])) {
                $role = $data['role'];
                unset($data['role']);
                
                $user->update($data);
                $user->syncRoles([$role]);
            } else {
                $user->update($data);
            }
            
            Log::info('User updated', [
                'user_id' => $id,
                'updated_by' => auth()->id(),
            ]);

            return $user->fresh()->load('roles');
        });
    }

    public function delete(int $id)
    {
        return DB::transaction(function () use ($id) {
            $result = $this->userRepository->delete($id);
            
            Log::info('User deleted', [
                'user_id' => $id,
                'deleted_by' => auth()->id(),
            ]);

            return $result;
        });
    }

    public function assignRole(User $user, string $role)
    {
        return DB::transaction(function () use ($user, $role) {
            $user->syncRoles([$role]);
            
            Log::info('Role assigned to user', [
                'user_id' => $user->id,
                'role' => $role,
                'assigned_by' => auth()->id(),
            ]);

            return $user->fresh()->load('roles');
        });
    }

    public function findByEmail(string $email)
    {
        return $this->userRepository->findByEmail($email);
    }
}