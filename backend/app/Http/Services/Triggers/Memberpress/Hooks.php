<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\Memberpress\MemberpressController;

Hooks::addAction('mepr-event-transaction-completed', [MemberpressController::class, 'oneTimeMembershipSubscribe'], 10, 1);
Hooks::addAction('mepr-event-transaction-completed', [MemberpressController::class, 'recurringMembershipSubscribe'], 10, 1);
Hooks::addAction('mepr_subscription_transition_status', [MemberpressController::class, 'membershipSubscribeCancel'], 10, 3);
Hooks::addAction('mepr-event-transaction-expired', [MemberpressController::class, 'membershipSubscribeExpire'], 10, 1);
Hooks::addAction('mepr_subscription_transition_status', [MemberpressController::class, 'membershipSubscribePaused'], 10, 3);
