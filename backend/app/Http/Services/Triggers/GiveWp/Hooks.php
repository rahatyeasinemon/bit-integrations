<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\GiveWp\GiveWpController;

Hooks::addAction('give_update_payment_status', [GiveWpController::class, 'handleUserDonation'], 10, 3);
Hooks::addAction('give_subscription_cancelled', [GiveWpController::class, 'handleSubscriptionDonationCancel'], 10, 2);
Hooks::addAction('give_subscription_updated', [GiveWpController::class, 'handleRecurringDonation'], 10, 4);
