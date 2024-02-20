<?php

if (!defined('ABSPATH')) {
    exit;
}
use BitApps\BTCBI\Util\Route;
use BitApps\BTCBI\Http\Services\Triggers\Met\MetController;

Route::get('met/get', [MetController::class, 'getAll']);
Route::post('met/get/form', [MetController::class, 'get_a_form']);
