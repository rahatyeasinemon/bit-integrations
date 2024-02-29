<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;
use BitApps\BTCBI\Http\Services\Triggers\EVF\EVFController;

Route::get('evf/get', [EVFController::class, 'getAll']);
Route::post('evf/get/form', [EVFController::class, 'getAForm']);
