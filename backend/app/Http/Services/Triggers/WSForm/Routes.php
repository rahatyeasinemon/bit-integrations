<?php

if (!defined('ABSPATH')) {
    exit;
}
use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Http\Services\Triggers\WSForm\WSFormController;

Route::get('wsform/get', [WSFormController::class, 'getAll']);
Route::post('wsform/get/form', [WSFormController::class, 'get_a_form']);
