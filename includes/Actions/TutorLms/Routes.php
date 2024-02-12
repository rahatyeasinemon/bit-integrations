<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\TutorLms\TutorLmsController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('tutor_authorize', [TutorLmsController::class, 'TutorAuthorize']);
Route::get('tutor_all_course', [TutorLmsController::class, 'getAllCourse']);
Route::get('tutor_all_lesson', [TutorLmsController::class, 'getAllLesson']);
