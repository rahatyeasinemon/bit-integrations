<?php


if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Route;
use BitApps\BTCBI\Http\Services\Triggers\Divi\DiviController;

Route::get('divi/get', [DiviController::class, 'getAllForms']);
Route::post('divi/get/form', [DiviController::class, 'getFormFields']);
