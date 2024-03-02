<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;
use BitApps\BTCBI\Http\Services\Triggers\UltimateMember\UltimateMemberController;

Route::get('ultimatemember/get', [UltimateMemberController::class, 'getAll']);
Route::post('ultimatemember/get/form', [UltimateMemberController::class, 'get_a_form']);

Route::get('get_um_all_role', [UltimateMemberController::class, 'getUMrole']);
