<?php


if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Triggers\Divi\DiviController;

Route::get('divi/get', [DiviController::class, 'getAllForms']);
Route::post('divi/get/form', [DiviController::class, 'getFormFields']);
