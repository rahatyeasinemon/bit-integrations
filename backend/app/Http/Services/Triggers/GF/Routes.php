<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Route;
use BitApps\BTCBI\Http\Services\Triggers\GF\GFController;

Route::get('gf/get', [GFController::class, 'getAll']);
Route::post('gf/get/form', [GFController::class, 'get_a_form']);
