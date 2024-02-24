<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\EDD\EDDController;

Hooks::addAction('edd_complete_purchase', [EDDController::class, 'handlePurchaseProduct'], 10, 1);
Hooks::addAction('edd_complete_purchase', [EDDController::class, 'handlePurchaseProductDiscountCode'], 10, 3);
Hooks::addAction('edds_payment_refunded', [EDDController::class, 'handleOrderRefunded'], 10, 1);
