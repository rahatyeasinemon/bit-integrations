<?php
if (!defined('ABSPATH')) {
    exit;
}
use BitCode\BTCBI\Core\Util\Route;
use BitCode\BTCBI\Triggers\WPCourseware\WPCoursewareController;

Route::get('wpcourseware/get', [WPCoursewareController::class, 'getAll']);
Route::post('wpcourseware/get/form', [WPCoursewareController::class, 'get_a_form']);