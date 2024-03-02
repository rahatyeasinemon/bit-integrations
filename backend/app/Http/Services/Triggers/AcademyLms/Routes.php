<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;
use BitApps\BTCBI\Http\Services\Triggers\AcademyLms\AcademyLmsController;

Route::get('academylms/get', [AcademyLmsController::class, 'getAll']);
Route::post('academylms/get/form', [AcademyLmsController::class, 'get_a_form']);
