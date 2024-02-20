<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Route;
use BitApps\BTCBI\Http\Services\Triggers\Themify\ThemifyController;

Route::get('themify/get', [ThemifyController::class, 'getAllForms']);
Route::post('themify/get/form', [ThemifyController::class, 'getFormFields']);
