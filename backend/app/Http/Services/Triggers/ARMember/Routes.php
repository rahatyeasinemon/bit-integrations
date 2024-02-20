<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Route;
use BitApps\BTCBI\Http\Services\Triggers\ARMember\ARMemberController;

Route::get('armember/get', [ARMemberController::class, 'getAll']);
Route::post('armember/get/form', [ARMemberController::class, 'get_a_form']);

// Route::get('get_lifterLms_all_quiz', [LifterLmsController::class, 'getLifterLmsAllQuiz']);
