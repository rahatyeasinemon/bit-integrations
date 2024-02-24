<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\TutorLms\TutorLmsController;

Hooks::addAction('tutor_after_enroll', [TutorLmsController::class, 'handle_course_enroll'], 10, 2);
Hooks::addAction('tutor_quiz/attempt_ended', [TutorLmsController::class, 'handleQuizAttempt'], 10, 1);
Hooks::addAction('tutor_lesson_completed_after', [TutorLmsController::class, 'handleLessonComplete'], 10, 1);
Hooks::addAction('tutor_course_complete_after', [TutorLmsController::class, 'handleCourseComplete'], 10, 1);
Hooks::addAction('tutor_quiz/attempt_ended', [TutorLmsController::class, 'handleQuizTarget'], 10, 1);
