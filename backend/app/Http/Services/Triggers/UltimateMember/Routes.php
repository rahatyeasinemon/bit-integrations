<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Triggers\UltimateMember\UltimateMemberController;

Route::get('ultimatemember/get', [UltimateMemberController::class, 'getAll']);
Route::post('ultimatemember/get/form', [UltimateMemberController::class, 'get_a_form']);

Route::get('get_um_all_role', [UltimateMemberController::class, 'getUMrole']);
