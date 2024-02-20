<?php


if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Route;
use BitApps\BTCBI\Http\Services\Triggers\Registration\RegistrationController;

Route::get('registration/get', [RegistrationController::class, 'getAll']);
Route::post('registration/get/form', [RegistrationController::class, 'get_a_form']);
