<?php


if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Http\Services\Triggers\Registration\RegistrationController;

Route::get('registration/get', [RegistrationController::class, 'getAll']);
Route::post('registration/get/form', [RegistrationController::class, 'get_a_form']);
