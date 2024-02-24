<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\WPCourseware\WPCoursewareController;

Hooks::addAction('wpcw_enroll_user', [WPCoursewareController::class, 'userEnrolledCourse'], 10, 2);
Hooks::addAction('wpcw_user_completed_course', [WPCoursewareController::class, 'courseCompleted'], 10, 3);
Hooks::addAction('wpcw_user_completed_module', [WPCoursewareController::class, 'moduleCompleted'], 10, 3);
Hooks::addAction('wpcw_user_completed_unit', [WPCoursewareController::class, 'unitCompleted'], 10, 3);
