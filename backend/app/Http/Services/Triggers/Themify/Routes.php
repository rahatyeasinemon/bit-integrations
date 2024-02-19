<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Http\Services\Triggers\Themify\ThemifyController;

Route::get('themify/get', [ThemifyController::class, 'getAllForms']);
Route::post('themify/get/form', [ThemifyController::class, 'getFormFields']);
