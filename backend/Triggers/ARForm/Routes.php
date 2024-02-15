<?php

if (!defined('ABSPATH')) {
    exit;
}
use BitCode\BTCBI\Core\Util\Route;
use BitCode\BTCBI\Triggers\ARForm\ARFormController;

Route::get('arform/get', [ARFormController::class, 'getAll']);
Route::post('arform/get/form', [ARFormController::class, 'get_a_form']);
