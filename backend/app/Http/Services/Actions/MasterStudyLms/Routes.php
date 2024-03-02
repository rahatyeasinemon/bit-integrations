<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\MasterStudyLms\MasterStudyLmsController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('MasterStudyLms_authorize', [MasterStudyLmsController::class, 'authorizeMasterStudyLms']);
Route::post('mslms_fetch_all_course', [MasterStudyLmsController::class, 'getAllCourse']);
Route::post('msLms_fetch_all_lesson', [MasterStudyLmsController::class, 'getAllLesson']);
Route::post('msLms_fetch_all_quiz', [MasterStudyLmsController::class, 'getAllQuizByCourse']);
