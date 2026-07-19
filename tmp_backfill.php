<?php

use App\Models\Store;
use App\Models\Role;
use App\Models\User;

$stores = Store::all();

$globalManager = Role::where('slug', 'manager')->whereNull('store_code')->first();
$globalStaff = Role::where('slug', 'staff')->whereNull('store_code')->first();

foreach ($stores as $store) {
    // Generate Manager
    $storeManager = Role::firstOrCreate(
        ['slug' => 'manager', 'store_code' => $store->code],
        ['name' => 'Manager']
    );
    if ($storeManager->wasRecentlyCreated && $globalManager) {
        $storeManager->permissions()->sync($globalManager->permissions->pluck('id'));
    }

    // Generate Staff
    $storeStaff = Role::firstOrCreate(
        ['slug' => 'staff', 'store_code' => $store->code],
        ['name' => 'Staff']
    );
    if ($storeStaff->wasRecentlyCreated && $globalStaff) {
        $storeStaff->permissions()->sync($globalStaff->permissions->pluck('id'));
    }

    // Reassign Users
    $users = User::where('store_code', $store->code)->get();
    foreach ($users as $user) {
        if ($user->role_id === $globalManager?->id) {
            $user->update(['role_id' => $storeManager->id]);
        } elseif ($user->role_id === $globalStaff?->id) {
            $user->update(['role_id' => $storeStaff->id]);
        }
    }
}

echo "Backfill completed successfully!\n";
