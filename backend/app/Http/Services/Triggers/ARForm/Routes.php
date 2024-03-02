<?php

if (!defined('ABSPATH')) {
    exit;
}
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;
use BitApps\BTCBI\Http\Services\Triggers\ARForm\ARFormController;

Route::get('arform/get', [ARFormController::class, 'getAll']);
Route::post('arform/get/form', [ARFormController::class, 'get_a_form']);
