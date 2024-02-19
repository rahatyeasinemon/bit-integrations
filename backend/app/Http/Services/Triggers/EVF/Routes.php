<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Http\Services\Triggers\EVF\EVFController;

Route::get('evf/get', [EVFController::class, 'getAll']);
Route::post('evf/get/form', [EVFController::class, 'getAForm']);
