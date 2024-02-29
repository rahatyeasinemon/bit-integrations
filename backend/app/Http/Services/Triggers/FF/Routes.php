<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;
use BitApps\BTCBI\Http\Services\Triggers\FF\FFController;

Route::get('ff/get', [FFController::class, 'getAll']);
Route::post('ff/get/form', [FFController::class, 'get_a_form']);
