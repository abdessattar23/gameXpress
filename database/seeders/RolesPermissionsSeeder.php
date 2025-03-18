<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create permissions
        Permission::create(['name' => 'view_dashboard', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'view_products', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'create_products', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'edit_products', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'delete_products', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'restore_products', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'view_categories', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'create_categories', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'edit_categories', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'delete_categories', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'view_users', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'create_users', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'edit_users', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'delete_users', 'guard_name' => 'sanctum']);

        // create roles
        $role_1 = Role::create(['name' => 'super_admin', 'guard_name' => 'sanctum']);
        $role_2 = Role::create(['name' => 'product_manager', 'guard_name' => 'sanctum']);
        $role_3 = Role::create(['name' => 'user_manager', 'guard_name' => 'sanctum']);
        $role_4 = Role::create(['name' => 'guest', 'guard_name' => 'sanctum']);
        
        // assign permissions to roles
        $role_1->givePermissionTo(Permission::where('guard_name', 'sanctum')->get());
        $role_2->givePermissionTo([
            'view_dashboard',
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            'restore_products',
        ]);
        $role_3->givePermissionTo([
            'view_dashboard',
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
        ]);
    }
}
