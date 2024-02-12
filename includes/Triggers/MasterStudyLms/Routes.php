<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Core\Util\Route;
use BitCode\BTCBI\Triggers\MasterStudyLms\MasterStudyLmsController;

Route::get('masterstudylms/get', [MasterStudyLmsController::class, 'getAll']);
Route::post('masterstudylms/get/form', [MasterStudyLmsController::class, 'get_a_form']);

Route::get('get_mslms_all_quiz_by_course', [MasterStudyLmsController::class, 'getAllQuizByCourse']);

// for edit
Route::get('get_masterStudyLms_all_course', [MasterStudyLmsController::class, 'getAllCourseEdit']);
Route::get('get_masterStudyLms_all_lesson', [MasterStudyLmsController::class, 'getAllLessonEdit']);
