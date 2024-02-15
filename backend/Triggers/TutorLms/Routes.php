<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Core\Util\Route;
use BitCode\BTCBI\Triggers\TutorLms\TutorLmsController;

Route::get('tutorlms/get', [TutorLmsController::class, 'getAll']);
Route::post('tutorlms/get/form', [TutorLmsController::class, 'get_a_form']);
