# Role-Based Access Control (RBAC) Documentation

This document explains the roles and permissions architecture implemented in the application.

---

## 1. Core Concepts: Roles vs. Permissions

- **Role**: Represents **"Who you are"** (e.g. `Developer`, `SuperAdmin`, `Admin`, `Manager`, `Staff`).
- **Permission**: Represents **"What you can do"** (granular actions, e.g. `view-users`, `create-users`, `edit-users`, `delete-users`, `access-developer-tools`).

### Why separate them?
Instead of checking roles directly in your code (which makes the application hard to maintain if roles change), you check for specific **permissions**.

For example, instead of writing:
```php
if ($user->role === 'admin' || $user->role === 'manager') { ... }
```
You write:
```php
if ($user->hasPermission('create-users')) { ... }
```
If you ever want to allow another role (like `Staff`) to create users, you simply assign the `create-users` permission to the `Staff` role in the database. **Your PHP code never has to change!**

---

## 2. Database Schema

The system uses **4 tables** to establish a clean one-to-many relationship between Users and Roles, and a many-to-many relationship between Roles and Permissions:

```
[ users ]
  - id (Primary Key)
  - name
  - email
  - role_id (Foreign Key pointing to roles.id)
  - department
  ...

      │
      ▼ (belongs to)
[ roles ]
  - id (Primary Key)
  - name (e.g. "Admin")
  - slug (e.g. "admin")

      │
      ▼ (many-to-many via pivot table)
[ permission_role ] (Pivot Table)
  - role_id (Foreign Key pointing to roles.id)
  - permission_id (Foreign Key pointing to permissions.id)

      ▲
      │ (many-to-many via pivot table)
[ permissions ]
  - id (Primary Key)
  - name (e.g. "Create Users")
  - slug (e.g. "create-users")
```

---

## 3. How to check Roles & Permissions in PHP

The `User` model (`app/Models/User.php`) provides helper methods to check authorization:

### Check if User has a Role
```php
if ($user->hasRole('admin')) {
    // User is an Admin
}
```

### Check if User has a Permission
```php
if ($user->hasPermission('delete-users')) {
    // User is allowed to delete users
}
```

---

## 4. Current Seeded Roles & Permissions Mapping

| Role | Department | Default Seeded Email | Permissions |
| :--- | :--- | :--- | :--- |
| **Developer** | Development | `developer@example.com` | All permissions + `access-developer-tools` |
| **SuperAdmin** | IT | `superadmin@example.com` | `view-users`, `create-users`, `edit-users`, `delete-users` |
| **Admin** | Operations | `admin@example.com` | `view-users`, `create-users`, `edit-users` (cannot delete) |
| **Manager** | Management | `manager@example.com` | `view-users`, `create-users`, `edit-users` |
| **Staff** | Sales | `staff@example.com` | `view-users` (read-only) |

*All accounts share the default password:* **`password`**
