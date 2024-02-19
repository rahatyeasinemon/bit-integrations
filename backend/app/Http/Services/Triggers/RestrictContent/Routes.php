<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Http\Services\Triggers\RestrictContent\RestrictContentController;

Route::get('restrictcontent/get', [RestrictContentController::class, 'getAll']);
Route::post('restrictcontent/get/form', [RestrictContentController::class, 'get_a_form']);
Route::get('restrictContent_Get_All_Levels', [RestrictContentController::class, 'get_all_membership']);
