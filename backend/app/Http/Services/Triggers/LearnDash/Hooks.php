<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\LearnDash\LearnDashController;

Hooks::addAction('learndash_update_course_access', [LearnDashController::class, 'handle_course_enroll'], 10, 4);
Hooks::addAction('learndash_course_completed', [LearnDashController::class, 'handle_course_completed'], 10, 1);
Hooks::addAction('learndash_lesson_completed', [LearnDashController::class, 'handle_lesson_completed'], 10, 1);
Hooks::addAction('learndash_topic_completed', [LearnDashController::class, 'handle_topic_completed'], 10, 1);
Hooks::addAction('learndash_quiz_submitted', [LearnDashController::class, 'handle_quiz_attempt'], 10, 2);
Hooks::addAction('ld_added_group_access', [LearnDashController::class, 'handle_added_group'], 10, 2);
Hooks::addAction('ld_removed_group_access', [LearnDashController::class, 'handle_removed_group'], 10, 2);
Hooks::addAction('learndash_assignment_uploaded', [LearnDashController::class, 'handle_assignment_submit'], 10, 2);
