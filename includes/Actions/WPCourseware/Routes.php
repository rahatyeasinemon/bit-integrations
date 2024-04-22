<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\WPCourseware\WPCoursewareController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('wpCourseware_authorize', [WPCoursewareController::class, 'wpCoursewareAuthorize']);
Route::post('wpCourseware_actions', [WPCoursewareController::class, 'WPCWActions']);
Route::post('wpCourseware_courses', [WPCoursewareController::class, 'WPCWCourses']);
