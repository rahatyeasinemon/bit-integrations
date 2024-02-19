<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Http\Services\Triggers\AcademyLms\AcademyLmsController;

Route::get('academylms/get', [AcademyLmsController::class, 'getAll']);
Route::post('academylms/get/form', [AcademyLmsController::class, 'get_a_form']);
