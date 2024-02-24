<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\SliceWp\SliceWpController;

Hooks::addAction('slicewp_insert_affiliate', [SliceWpController::class, 'newAffiliateCreated'], 10, 2);
Hooks::addAction('slicewp_insert_commission', [SliceWpController::class, 'userEarnCommission'], 10, 2);
