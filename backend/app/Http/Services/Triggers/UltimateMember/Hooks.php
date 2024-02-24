<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\UltimateMember\UltimateMemberController;

Hooks::addAction('um_user_login', [UltimateMemberController::class, 'handleUserLogViaForm'], 9, 1);
Hooks::addAction('um_registration_complete', [UltimateMemberController::class, 'handleUserRegisViaForm'], 10, 2);
Hooks::addAction('set_user_role', [UltimateMemberController::class, 'handleUserRoleChange'], 10, 3);
Hooks::addAction('set_user_role', [UltimateMemberController::class, 'handleUserSpecificRoleChange'], 10, 3);
