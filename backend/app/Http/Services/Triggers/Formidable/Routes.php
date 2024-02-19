<?php

if (!defined('ABSPATH')) {
    exit;
}
use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Http\Services\Triggers\Formidable\FormidableController;

Route::get('formidable/get', [FormidableController::class, 'getAll']);
Route::post('formidable/get/form', [FormidableController::class, 'get_a_form']);
