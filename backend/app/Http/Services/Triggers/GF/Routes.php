<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Triggers\GF\GFController;

Route::get('gf/get', [GFController::class, 'getAll']);
Route::post('gf/get/form', [GFController::class, 'get_a_form']);
