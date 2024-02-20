<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Route;
use BitApps\BTCBI\Http\Services\Triggers\TutorLms\TutorLmsController;

Route::get('tutorlms/get', [TutorLmsController::class, 'getAll']);
Route::post('tutorlms/get/form', [TutorLmsController::class, 'get_a_form']);
