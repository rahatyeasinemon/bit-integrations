<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Core\Util\Route;
use BitCode\BTCBI\Triggers\LearnDash\LearnDashController;

Route::get('learndash/get', [LearnDashController::class, 'getAll']);
Route::post('learndash/get/form', [LearnDashController::class, 'get_a_form']);
Route::get('get_all_lessons_by_course', [LearnDashController::class, 'getLessonsByCourse']);
Route::get('get_all_topic_by_lesson', [LearnDashController::class, 'getTopicsByLesson']);
Route::get('get_all_courses', [LearnDashController::class, 'getCourses']);
Route::get('get_all_quizes', [LearnDashController::class, 'getQuizes']);
Route::get('get_all_groups', [LearnDashController::class, 'getGroups']);
