<?php

namespace App\Enums;

enum UserPaidStatus: string
{
    case PAID = 'paid';
    case FREE = 'free';
}
