<?php

if (!defined('ABSPATH')) {
    exit;
}
use BitApps\BTCBI\Util\Route;
use BitApps\BTCBI\Http\Services\Triggers\Happy\HappyController;

Route::get('happy/get', [HappyController::class, 'getAll']);
Route::post('happy/get/form', [HappyController::class, 'get_a_form']);
