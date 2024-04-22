<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\AcademyLms\AcademyLmsController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('academy_lms_authorize', [AcademyLmsController::class, 'Authorization']);
Route::get('academy_lms_all_course', [AcademyLmsController::class, 'getAllCourse']);
Route::get('academy_lms_all_lesson', [AcademyLmsController::class, 'getAllLesson']);
