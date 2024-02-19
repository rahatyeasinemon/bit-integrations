<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Http\Services\Triggers\WPEF\WPEFController;

Route::get('wpef/get', [WPEFController::class, 'getAll']);
Route::post('wpef/get/form', [WPEFController::class, 'getAForm']);
