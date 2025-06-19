<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case COWORKER = 'coworker';
    case CLIENT = 'client';
    case V1 = 'v1';
}