<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\RestrictContent\RestrictContentController;

Hooks::addAction('rcp_membership_post_activate', [RestrictContentController::class, 'purchasesMembershipLevel'], 10, 2);
Hooks::addAction('rcp_transition_membership_status_cancelled', [RestrictContentController::class, 'membershipStatusCancelled'], 10, 2);
Hooks::addAction('rcp_transition_membership_status_expired', [RestrictContentController::class, 'membershipStatusExpired'], 10, 2);
