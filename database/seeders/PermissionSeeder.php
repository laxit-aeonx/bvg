<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permission
        Permission::create(['name' => 'permission-list']);

        // Roles
        Permission::create(['name' => 'role-list']);
        Permission::create(['name' => 'role-view']);
        Permission::create(['name' => 'role-edit']);
        Permission::create(['name' => 'role-revoke']);
        Permission::create(['name' => 'role-delete']);

        // Permissions::User
        Permission::create(['name' => 'user-list']);
        Permission::create(['name' => 'user-create']);
        Permission::create(['name' => 'user-delete']);

        // By default admin will inherit all the permissions
        $superRole = Role::create(['name' => 'super-admin']);
        $superRole->givePermissionTo(Permission::all());

        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo([
            'user-list',
        ]); // Project Admins

        $role = Role::create(['name' => 'user']);
        $role->givePermissionTo([
            'user-list',
        ]); // Users
    }
}
