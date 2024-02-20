<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Route;
use BitApps\BTCBI\Http\Services\Triggers\BitForm\BitFormController;

Route::get('bitform/get', [BitFormController::class, 'getAll']);
Route::post('bitform/get/form', [BitFormController::class, 'get_a_form']);
