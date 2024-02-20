<?php

if (!defined('ABSPATH')) {
    exit;
}
use BitApps\BTCBI\Util\Route;
use BitApps\BTCBI\Http\Services\Triggers\WSForm\WSFormController;

Route::get('wsform/get', [WSFormController::class, 'getAll']);
Route::post('wsform/get/form', [WSFormController::class, 'get_a_form']);
