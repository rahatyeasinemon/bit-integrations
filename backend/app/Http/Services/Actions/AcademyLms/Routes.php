<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\AcademyLms\AcademyLmsController;

use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('academy_lms_authorize', [AcademyLmsController::class, 'Authorization']);
Route::get('academy_lms_all_course', [AcademyLmsController::class, 'getAllCourse']);
Route::get('academy_lms_all_lesson', [AcademyLmsController::class, 'getAllLesson']);
