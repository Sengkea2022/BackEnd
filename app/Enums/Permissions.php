<?php

namespace App\Enums;

enum Permissions: string
{
    case DEVELOPER = 'developer';

    case USER = 'user';
    
    case SUPER_STAFF = 'super_staff';
    case STAFF = 'staff';
    
}
