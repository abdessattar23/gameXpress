<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesController extends Controller
{
    public function index()
    {
        if (!auth()->user()->can('view_users')) {
            return response()->json([
                'message' => 'You do not have permission to view roles'
            ], 403);
        }

        $roles = Role::with('permissions')->get();

        return response()->json([
            'message' => 'Success',
            'data' => $roles
        ], 200);
    }

    
}
