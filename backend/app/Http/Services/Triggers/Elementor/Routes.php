<?php


if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Route;
use BitApps\BTCBI\Http\Services\Triggers\Elementor\ElementorController;

Route::get('elementor/get', [ElementorController::class, 'getAllForms']);
Route::post('elementor/get/form', [ElementorController::class, 'getFormFields']);
