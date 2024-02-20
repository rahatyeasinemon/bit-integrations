<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Route;
use BitApps\BTCBI\Http\Services\Triggers\EVF\EVFController;

Route::get('evf/get', [EVFController::class, 'getAll']);
Route::post('evf/get/form', [EVFController::class, 'getAForm']);
