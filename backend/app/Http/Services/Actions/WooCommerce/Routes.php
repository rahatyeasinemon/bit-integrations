<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\WooCommerce\WooCommerceController;
use BitApps\BTCBI\Util\Route;

Route::post('wc_authorize', [WooCommerceController::class, 'authorizeWC']);
Route::post('wc_refresh_fields', [WooCommerceController::class, 'refreshFields']);
Route::post('wc_search_products', [WooCommerceController::class, 'searchProjects']);
Route::post('wc_get_all_subscriptions_products', [WooCommerceController::class, 'allSubscriptionsProducts']);
