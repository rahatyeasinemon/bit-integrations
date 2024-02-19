<?php


if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Http\Services\Triggers\Elementor\ElementorController;

Route::get('elementor/get', [ElementorController::class, 'getAllForms']);
Route::post('elementor/get/form', [ElementorController::class, 'getFormFields']);
