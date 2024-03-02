<?php

if (!defined('ABSPATH')) {
    exit;
}
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;
use BitApps\BTCBI\Http\Services\Triggers\WPCourseware\WPCoursewareController;

Route::get('wpcourseware/get', [WPCoursewareController::class, 'getAll']);
Route::post('wpcourseware/get/form', [WPCoursewareController::class, 'get_a_form']);
