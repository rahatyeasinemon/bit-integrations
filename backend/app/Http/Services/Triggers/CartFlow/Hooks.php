<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\CartFlow\CartFlowController;

Hooks::addAction('woocommerce_checkout_order_processed', [CartFlowController::class, 'handle_order_create_wc'], 10, 2);
