<?php

if (!defined('ABSPATH')) {
    exit;
}
use BitApps\BTCBI\Util\Route;
use BitApps\BTCBI\Http\Services\Triggers\Formidable\FormidableController;

Route::get('formidable/get', [FormidableController::class, 'getAll']);
Route::post('formidable/get/form', [FormidableController::class, 'get_a_form']);
