<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        // Only users with permission can list all users
        if (!$request->user()->hasPermission('view_users')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $users = User::with('roles')->paginate(10);
        return UserResource::collection($users);
    }

    public function show(Request $request, User $user)
    {
        // Users can view their own profile or admin can view any profile
        if ($request->user()->id !== $user->id && !$request->user()->hasPermission('view_users')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return new UserResource($user->load('roles'));
    }

    public function update(Request $request, User $user)
    {
        // Users can edit their own profile or admin can edit any profile
        if ($request->user()->id !== $user->id && !$request->user()->hasPermission('edit_users')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        $data = $request->only(['name', 'email']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return new UserResource($user->load('roles'));
    }

    public function assignRole(Request $request, User $user)
    {
        // Only users with permission can assign roles
        if (!$request->user()->hasPermission('assign_roles')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->roles()->syncWithoutDetaching($request->role_id);

        return new UserResource($user->load('roles'));
    }

    public function removeRole(Request $request, User $user)
    {
        // Only users with permission can remove roles
        if (!$request->user()->hasPermission('assign_roles')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->roles()->detach($request->role_id);

        return new UserResource($user->load('roles'));
    }
}
