<?php

if (!defined('ABSPATH')) {
    exit;
}
use BitApps\BTCBI\Util\Route;
use BitApps\BTCBI\Http\Services\Triggers\WeForms\WeFormsController;

Route::get('weforms/get', [WeFormsController::class, 'getAll']);
Route::post('weforms/get/form', [WeFormsController::class, 'get_a_form']);
