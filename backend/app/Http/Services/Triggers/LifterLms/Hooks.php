<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\LifterLms\LifterLmsController;

Hooks::addAction('lifterlms_quiz_completed', [LifterLmsController::class, 'handleAttemptQuiz'], 10, 3);
Hooks::addAction('lifterlms_quiz_passed', [LifterLmsController::class, 'handleQuizPass'], 10, 3);
Hooks::addAction('lifterlms_quiz_failed', [LifterLmsController::class, 'handleQuizFail'], 10, 3);
Hooks::addAction('lifterlms_lesson_completed', [LifterLmsController::class, 'handleLessonComplete'], 10, 2);
Hooks::addAction('lifterlms_course_completed', [LifterLmsController::class, 'handleCourseComplete'], 10, 2);
Hooks::addAction('llms_user_enrolled_in_course', [LifterLmsController::class, 'handleCourseEnroll'], 10, 2);
Hooks::addAction('llms_user_removed_from_course', [LifterLmsController::class, 'handleCourseUnEnroll'], 10, 4);
Hooks::addAction('llms_subscription_cancelled_by_student', [LifterLmsController::class, 'handleMembershipCancel'], 10, 4);
