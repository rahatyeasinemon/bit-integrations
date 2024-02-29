<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;
use BitApps\BTCBI\Http\Services\Triggers\Themify\ThemifyController;

Route::get('themify/get', [ThemifyController::class, 'getAllForms']);
Route::post('themify/get/form', [ThemifyController::class, 'getFormFields']);
