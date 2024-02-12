<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Core\Util\Hooks;
use BitCode\BTCBI\Triggers\SolidAffiliate\SolidAffiliateController;

Hooks::add('data_model_solid_affiliate_affiliates_save', [SolidAffiliateController::class, 'newSolidAffiliateCreated'], 10, 1);
Hooks::add('data_model_solid_affiliate_referrals_save', [SolidAffiliateController::class, 'newSolidAffiliateReferralCreated'], 10, 1);
