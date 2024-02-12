<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\WPCourseware\WPCoursewareController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('wpCourseware_authorize', [WPCoursewareController::class, 'wpCoursewareAuthorize']);
Route::post('wpCourseware_actions', [WPCoursewareController::class, 'WPCWActions']);
Route::post('wpCourseware_courses', [WPCoursewareController::class, 'WPCWCourses']);
