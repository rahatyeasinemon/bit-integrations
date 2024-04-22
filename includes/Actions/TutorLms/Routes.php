<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\TutorLms\TutorLmsController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('tutor_authorize', [TutorLmsController::class, 'TutorAuthorize']);
Route::get('tutor_all_course', [TutorLmsController::class, 'getAllCourse']);
Route::get('tutor_all_lesson', [TutorLmsController::class, 'getAllLesson']);
