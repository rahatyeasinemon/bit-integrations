<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\SolidAffiliate\SolidAffiliateController;

Hooks::addAction('data_model_solid_affiliate_affiliates_save', [SolidAffiliateController::class, 'newSolidAffiliateCreated'], 10, 1);
Hooks::addAction('data_model_solid_affiliate_referrals_save', [SolidAffiliateController::class, 'newSolidAffiliateReferralCreated'], 10, 1);
