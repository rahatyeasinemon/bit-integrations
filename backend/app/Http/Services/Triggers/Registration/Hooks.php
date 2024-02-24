<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\Registration\RegistrationController;

//* User Register HOOK*//
Hooks::addAction('user_register', [RegistrationController::class, 'userCreate'], 10, 2);
//* User Register HOOK*//

//* User Profile Update HOOK*//
Hooks::addAction('profile_update', [RegistrationController::class, 'profileUpdate'], 10, 3);
Hooks::addAction('wp_login', [RegistrationController::class, 'wpLogin'], 10, 2);
Hooks::addAction('password_reset', [RegistrationController::class, 'wpResetPassword'], 10, 1);
Hooks::addAction('delete_user', [RegistrationController::class, 'wpUserDeleted'], 10, 3);
