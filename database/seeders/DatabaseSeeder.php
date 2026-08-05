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

        $viewProducts = Permission::create(['name' => 'View Products', 'slug' => 'view-products']);
        $createProducts = Permission::create(['name' => 'Create Products', 'slug' => 'create-products']);
        $editProducts = Permission::create(['name' => 'Edit Products', 'slug' => 'edit-products']);
        $deleteProducts = Permission::create(['name' => 'Delete Products', 'slug' => 'delete-products']);

        // 2. Seed Roles
        $developerRole   = Role::create(['name' => 'Developer',  'slug' => 'developer']);
        $shopOwnerRole   = Role::create(['name' => 'Shop Owner', 'slug' => 'shop-owner']);
        $managerRole     = Role::create(['name' => 'Manager',    'slug' => 'manager']);
        $staffRole       = Role::create(['name' => 'Staff',      'slug' => 'staff']);

        // 3. Attach Permissions to Roles
        // Developer gets everything
        $developerRole->permissions()->attach([
            $viewUsers->id, $editUsers->id, $createUsers->id, $deleteUsers->id,
            $viewProducts->id, $createProducts->id, $editProducts->id, $deleteProducts->id
        ]);
        
        // Shop Owner gets everything
        $shopOwnerRole->permissions()->attach([
            $viewUsers->id, $editUsers->id, $createUsers->id, $deleteUsers->id,
            $viewProducts->id, $createProducts->id, $editProducts->id, $deleteProducts->id
        ]);
        
        // Manager can't delete users or delete products
        $managerRole->permissions()->attach([
            $viewUsers->id, $editUsers->id, $createUsers->id,
            $viewProducts->id, $createProducts->id, $editProducts->id
        ]);
        
        // Staff can only view/create products, but cannot edit or delete them. Cannot manage users.
        $staffRole->permissions()->attach([
            $viewProducts->id, $createProducts->id
        ]);

        // 4. Create Test Users and Assign Roles
        User::factory()->create([
            'name'       => 'Developer User',
            'email'      => 'developer@example.com',
            'department' => 'IT',
            'position'   => Positions::IT_DIRECTOR,
            'role_id'    => $developerRole->id,
        ]);

        User::factory()->create([
            'name'       => 'Shop Owner',
            'email'      => 'shopowner@example.com',
            'department' => 'Operations',
            'position'   => Positions::ADMINISTRATOR,
            'role_id'    => $shopOwnerRole->id,
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
