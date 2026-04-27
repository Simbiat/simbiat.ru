<?php
declare(strict_types = 1);

namespace App\Enum;

/**
 * IDs of system users
 */
enum LogType: int
{
    case Avatar = 1;
    case Ban = 2;
    case BICTracker = 3;
    case CSRF = 4;
    case Email = 5;
    case FailedLogin = 6;
    case FileUpload = 7;
    case Login = 8;
    case Logout = 9;
    case PasswordChange = 10;
    case PasswordReset = 11;
    case UserDetailsChanged = 12;
    case UserRemoval = 13;
}