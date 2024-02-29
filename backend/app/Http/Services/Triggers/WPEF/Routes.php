<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;
use BitApps\BTCBI\Http\Services\Triggers\WPEF\WPEFController;

Route::get('wpef/get', [WPEFController::class, 'getAll']);
Route::post('wpef/get/form', [WPEFController::class, 'getAForm']);
