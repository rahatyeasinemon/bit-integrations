<?php

if (!defined('ABSPATH')) {
    exit;
}
use BitApps\BTCBI_PRO\Core\Util\Route;
use BitApps\BTCBI_PRO\Triggers\WC\WCController;

Route::get('wc/get', [WCController::class, 'getAll']);
Route::post('wc/get/form', [WCController::class, 'get_trigger_field']);

// get order status
Route::get('get_all_order_status', [WCController::class, 'getOrderStatus']);
Route::get('get_all_subscription_product', [WCController::class, 'getSubscriptionProduct']);
Route::get('get_all_subscription_status', [WCController::class, 'getSubscriptionStatus']);
Route::get('get_all_woocommerce_product', [WCController::class, 'getWooCommerceProduct']);
Route::get('get_all_product_category', [WCController::class, 'getProductCategories']);
Route::get('get_all_variation_by_product', [WCController::class, 'getVariationOfProduct']);
