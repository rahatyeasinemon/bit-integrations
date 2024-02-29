<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\ZohoMarketingHub\ZohoMarketingHubController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('zmarketingHub_generate_token', [ ZohoMarketingHubController::class, 'generateTokens']);
Route::post('zmarketingHub_refresh_lists', [ ZohoMarketingHubController::class, 'refreshLists']);
Route::post('zmarketingHub_refresh_contact_fields', [ ZohoMarketingHubController::class, 'refreshContactFields']);
