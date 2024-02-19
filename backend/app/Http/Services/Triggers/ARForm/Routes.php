<?php

if (!defined('ABSPATH')) {
    exit;
}
use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Http\Services\Triggers\ARForm\ARFormController;

Route::get('arform/get', [ARFormController::class, 'getAll']);
Route::post('arform/get/form', [ARFormController::class, 'get_a_form']);
