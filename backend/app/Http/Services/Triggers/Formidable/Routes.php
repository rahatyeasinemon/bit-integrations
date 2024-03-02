<?php

if (!defined('ABSPATH')) {
    exit;
}
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;
use BitApps\BTCBI\Http\Services\Triggers\Formidable\FormidableController;

Route::get('formidable/get', [FormidableController::class, 'getAll']);
Route::post('formidable/get/form', [FormidableController::class, 'get_a_form']);
