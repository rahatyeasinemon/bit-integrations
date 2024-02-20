<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\ZohoAnalytics\ZohoAnalyticsController;
use BitApps\BTCBI\Util\Route;

Route::post('zanalytics_generate_token', [ZohoAnalyticsController::class, 'generateTokens']);
Route::post('zanalytics_refresh_workspaces', [ZohoAnalyticsController::class, 'refreshWorkspacesAjaxHelper']);
Route::post('zanalytics_refresh_users', [ZohoAnalyticsController::class, 'refreshUsersAjaxHelper']);
Route::post('zanalytics_refresh_tables', [ZohoAnalyticsController::class, 'refreshTablesAjaxHelper']);
Route::post('wp_ajax_zanalytics_refresh_table_headers', [ZohoAnalyticsController::class, 'refreshTableHeadersAjaxHelper']);

// public static function registerAjax()
// {
//     add_action('wp_ajax_zanalytics_generate_token', array(__CLASS__, 'generateTokens'));
//     add_action('wp_ajax_zanalytics_refresh_workspaces', array(__CLASS__, 'refreshWorkspacesAjaxHelper'));
//     add_action('wp_ajax_zanalytics_refresh_users', array(__CLASS__, 'refreshUsersAjaxHelper'));
//     add_action('wp_ajax_zanalytics_refresh_tables', array(__CLASS__, 'refreshTablesAjaxHelper'));
//     add_action('wp_ajax_zanalytics_refresh_table_headers', array(__CLASS__, 'refreshTableHeadersAjaxHelper'));
// }
