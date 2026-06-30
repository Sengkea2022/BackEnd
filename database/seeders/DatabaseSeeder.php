<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Enums\Positions;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Permissions
        $viewUsers = Permission::create(['name' => 'View Users', 'slug' => 'view-users']);
        $createUsers = Permission::create(['name' => 'Create Users', 'slug' => 'create-users']);
        $editUsers = Permission::create(['name' => 'Edit Users', 'slug' => 'edit-users']);
        $deleteUsers = Permission::create(['name' => 'Delete Users', 'slug' => 'delete-users']);
        $devTools = Permission::create(['name' => 'Access Developer Tools', 'slug' => 'access-developer-tools']);

        // 2. Seed Roles
        $developerRole = Role::create(['name' => 'Developer', 'slug' => 'developer']);
        $superadminRole = Role::create(['name' => 'SuperAdmin', 'slug' => 'superadmin']);
        $adminRole = Role::create(['name' => 'Admin', 'slug' => 'admin']);
        $managerRole = Role::create(['name' => 'Manager', 'slug' => 'manager']);
        $staffRole = Role::create(['name' => 'Staff', 'slug' => 'staff']);

        // 3. Attach Permissions to Roles
        $developerRole->permissions()->attach([$viewUsers->id, $editUsers->id, $createUsers->id, $deleteUsers->id, $devTools->id]);
        $superadminRole->permissions()->attach([$viewUsers->id, $editUsers->id, $createUsers->id, $deleteUsers->id]);
        $adminRole->permissions()->attach([$viewUsers->id, $editUsers->id, $createUsers->id]); // Admin cannot delete users
        $managerRole->permissions()->attach([$viewUsers->id, $editUsers->id, $createUsers->id]);
        $staffRole->permissions()->attach([$viewUsers->id]);

        // 4. Create Test Users and Assign Roles
        User::factory()->create([
            'name' => 'Developer User',
            'email' => 'developer@example.com',
            'department' => 'IT',
            'position' => Positions::TECH_LEAD,
            'role_id' => $developerRole->id,
        ]);

        User::factory()->create([
            'name' => 'Super Admin User',
            'email' => 'superadmin@example.com',
            'department' => 'IT',
            'position' => Positions::IT_DIRECTOR,
            'role_id' => $superadminRole->id,
        ]);

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'department' => 'Operations',
            'position' => Positions::ADMINISTRATOR,
            'role_id' => $adminRole->id,
        ]);

        User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'department' => 'Management',
            'position' => Positions::STORE_MANAGER,
            'role_id' => $managerRole->id,
        ]);

        User::factory()->create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'department' => 'Sales',
            'position' => Positions::CASHIER,
            'role_id' => $staffRole->id,
        ]);
    }
}
