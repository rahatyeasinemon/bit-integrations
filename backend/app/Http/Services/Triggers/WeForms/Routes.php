<?php

if (!defined('ABSPATH')) {
    exit;
}
use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Http\Services\Triggers\WeForms\WeFormsController;

Route::get('weforms/get', [WeFormsController::class, 'getAll']);
Route::post('weforms/get/form', [WeFormsController::class, 'get_a_form']);
