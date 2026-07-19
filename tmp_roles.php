<?php

use App\Models\Role;
use App\Models\Permission;

// 1. Delete Developer
Role::where('slug', 'developer')->delete();

// 2. Rename Admin to Store Owner
Role::where('slug', 'admin')->update([
    'name' => 'Store Owner',
    'slug' => 'store-owner'
]);

// 3. Create Product Permissions
$vp = Permission::firstOrCreate(['slug' => 'view-products'], ['name' => 'View Products']);
$cp = Permission::firstOrCreate(['slug' => 'create-products'], ['name' => 'Create Products']);
$ep = Permission::firstOrCreate(['slug' => 'edit-products'], ['name' => 'Edit Products']);
$dp = Permission::firstOrCreate(['slug' => 'delete-products'], ['name' => 'Delete Products']);

// 4. Assign permissions to SuperAdmin
$sa = Role::where('slug', 'superadmin')->first();
if ($sa) {
    $sa->permissions()->syncWithoutDetaching([$vp->id, $cp->id, $ep->id, $dp->id]);
}

// 5. Assign permissions to Store Owner
$so = Role::where('slug', 'store-owner')->first();
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
