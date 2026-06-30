<?php

namespace App\Enums;

enum Permissions: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';

    case USER = 'user';
    
    case SUPER_STAFF = 'super_staff';
    case STAFF = 'staff';
    
}
