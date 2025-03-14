<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index()
    {
        if (!auth()->user()->can('view_users')) {
            return response()->json([
                'message' => 'You do not have permission to view users'
            ], 403);
        }

        $users = User::with('roles')->get();

        return response()->json([
            'message' => 'Success',
            'data' => $users
        ], 200);
    }


    public function store(Request $request)
    {
        if (!auth()->user()->can('create_users')) {
            return response()->json([
                'message' => 'You do not have permission to create users'
            ], 403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'roles' => 'sometimes',
            'roles.*' => 'string|exists:roles,name'
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);


        if (!empty($data['roles'])) {
            $user->assignRole($data['roles']);
        } else {
            $user->assignRole('guest');
        }

        return response()->json([
            'message' => 'User created successfully',
            'data' => $user->load('roles')
        ], 201);
    }


    public function show($id)
    {
        if (!auth()->user()->can('view_users')) {
            return response()->json([
                'message' => 'You do not have permission to view users'
            ], 403);
        }

        $user = User::with('roles')->find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
                'status' => 'error 404'
            ], 404);
        }

        return response()->json([
            'message' => 'Success',
            'data' => $user
        ], 200);
    }


    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('edit_users')) {
            return response()->json([
                'message' => 'You do not have permission to update users'
            ], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
                'status' => 'error 404'
            ], 404);
        }

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8',
            'roles' => 'sometimes|array',
            'roles.*' => 'string|exists:roles,name'
        ]);


        if (isset($data['name'])) {
            $user->name = $data['name'];
        }

        if (isset($data['email'])) {
            $user->email = $data['email'];
        }

        if (isset($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        if (!empty($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user->load('roles')
        ], 200);
    }


    public function destroy($id)
    {
        if (!auth()->user()->can('delete_users')) {
            return response()->json([
                'message' => 'You do not have permission to delete users'
            ], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
                'status' => 'error 404'
            ], 404);
        }

        if ($user->id === 1) {
            return response()->json([
                'message' => 'You cannot delete this users account',
                'status' => 'error 400'
            ], 400);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ], 200);
    }
    public function physicDelete($id){

        if (!auth()->user()->can('delete_users')) {
            return response()->json([
                'message' => 'You do not have permission to delete users'
            ], 403);
        }
        $user = User::withTrashed()->find($id);

        if (!$user){
            return response()->json([
                'message' => 'User not found',
                'status' => 'error 404'
            ], 404);
        }

        $user->forceDelete();
        return response()->json([
            'message' => 'User deleted successfully'
        ], 200);

    }


}
