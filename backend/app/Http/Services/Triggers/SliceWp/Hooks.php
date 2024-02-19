<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Hooks;
use BitCode\BTCBI\Triggers\SliceWp\SliceWpController;

Hooks::add('slicewp_insert_affiliate', [SliceWpController::class, 'newAffiliateCreated'], 10, 2);
Hooks::add('slicewp_insert_commission', [SliceWpController::class, 'userEarnCommission'], 10, 2);
