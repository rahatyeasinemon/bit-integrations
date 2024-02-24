<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\MasterStudyLms\MasterStudyLmsController;

Hooks::addAction('stm_lms_progress_updated', [MasterStudyLmsController::class, 'handleCourseComplete'], 10, 3);
Hooks::addAction('course_enrolled', [MasterStudyLmsController::class, 'handleCourseEnroll'], 10, 2);
Hooks::addAction('lesson_completed', [MasterStudyLmsController::class, 'handleLessonComplete'], 10, 2);
Hooks::addAction('stm_lms_quiz_passed', [MasterStudyLmsController::class, 'handleQuizComplete'], 10, 3);
Hooks::addAction('stm_lms_quiz_failed', [MasterStudyLmsController::class, 'handleQuizFailed'], 10, 3);
