<?php

if (!defined('ABSPATH')) {
    exit;
}
use BitApps\BTCBI\Util\Route;
use BitApps\BTCBI\Http\Services\Triggers\ARForm\ARFormController;

Route::get('arform/get', [ARFormController::class, 'getAll']);
Route::post('arform/get/form', [ARFormController::class, 'get_a_form']);
