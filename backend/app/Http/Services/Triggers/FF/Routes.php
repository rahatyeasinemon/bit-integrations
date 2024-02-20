<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Route;
use BitApps\BTCBI\Http\Services\Triggers\FF\FFController;

Route::get('ff/get', [FFController::class, 'getAll']);
Route::post('ff/get/form', [FFController::class, 'get_a_form']);
