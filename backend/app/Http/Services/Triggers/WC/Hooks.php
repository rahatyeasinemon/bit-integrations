<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\WC\WCController;

Hooks::addAction('user_register', [WCController::class, 'handle_customer_create'], 10, 2);
Hooks::addAction('profile_update', [WCController::class, 'handle_customer_update'], 10, 3);
Hooks::addAction('delete_user', [WCController::class, 'handle_customer_delete'], 10, 1);

Hooks::addAction('transition_post_status', [WCController::class, 'handle_product_action'], 10, 3);

Hooks::addAction('woocommerce_checkout_order_processed', [WCController::class, 'handle_order_create'], 10, 2);
Hooks::addAction('save_post', [WCController::class, 'handle_order_update'], 10, 3);
Hooks::addAction('wp_trash_post', [WCController::class, 'handle_order_delete'], 10, 1);
Hooks::addAction('woocommerce_order_status_changed', [WCController::class, 'handle_order_status_change'], 10, 4);
Hooks::addAction('woocommerce_subscription_payment_complete', [WCController::class, 'handle_subscription_create'], 10, 1);
Hooks::addAction('woocommerce_subscription_status_cancelled', [WCController::class, 'handle_subscription_cancel'], 10, 1);
Hooks::addAction('woocommerce_subscription_status_expired', [WCController::class, 'handle_subscription_expired'], 10, 1);
Hooks::addAction('woocommerce_subscription_status_updated', [WCController::class, 'handle_subscription_status_change'], 10, 3);
Hooks::addAction('woocommerce_scheduled_subscription_trial_end', [WCController::class, 'handle_subscription_trial_period_end'], 10, 1);
Hooks::addAction('woocommerce_new_booking', [WCController::class, 'handle_booking_create'], 10, 1);
Hooks::addAction('comment_post', [WCController::class, 'handle_insert_comment'], 10, 3);

Hooks::addAction('woocommerce_checkout_order_processed', [WCController::class, 'handle_variable_product_order'], 10, 2);
