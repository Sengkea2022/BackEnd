<?php

use App\Models\Role;
use App\Models\Permission;

// 1. Rename SuperAdmin → Developer
Role::where('slug', 'superadmin')->update([
    'name' => 'Developer',
    'slug' => 'developer',
]);

// 2. Rename Admin / Store Owner → Shop Owner
Role::whereIn('slug', ['admin', 'store-owner'])->update([
    'name' => 'Shop Owner',
    'slug' => 'shop-owner',
]);

// 3. Ensure Product Permissions exist
$vp = Permission::firstOrCreate(['slug' => 'view-products'],   ['name' => 'View Products']);
$cp = Permission::firstOrCreate(['slug' => 'create-products'], ['name' => 'Create Products']);
$ep = Permission::firstOrCreate(['slug' => 'edit-products'],   ['name' => 'Edit Products']);
$dp = Permission::firstOrCreate(['slug' => 'delete-products'], ['name' => 'Delete Products']);

// 4. Assign permissions to Developer
$dev = Role::where('slug', 'developer')->first();
if ($dev) {
    $dev->permissions()->syncWithoutDetaching([$vp->id, $cp->id, $ep->id, $dp->id]);
}

// 5. Assign permissions to Shop Owner
$so = Role::where('slug', 'shop-owner')->first();
if ($so) {
    $so->permissions()->syncWithoutDetaching([$vp->id, $cp->id, $ep->id, $dp->id]);
}

// 6. Assign permissions to Manager
$mgr = Role::where('slug', 'manager')->first();
if ($mgr) {
    $mgr->permissions()->syncWithoutDetaching([$vp->id, $cp->id, $ep->id]);
}

// 7. Assign permissions to Staff
$stf = Role::where('slug', 'staff')->first();
if ($stf) {
    $stf->permissions()->syncWithoutDetaching([$vp->id, $cp->id]);
}

echo "Roles and Permissions updated successfully!\n";
