<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\Affiliate\AffiliateController;

Hooks::addAction('affwp_set_affiliate_status', [AffiliateController::class, 'newAffiliateApproved'], 10, 3);
// Hooks::addAction('affwp_register_user', [AffiliateController::class, 'newAffiliatAwaitingApproval'], 15, 3);
Hooks::addAction('affwp_set_affiliate_status', [AffiliateController::class, 'userBecomesAffiliate'], 10, 3);
Hooks::addAction('affwp_insert_referral', [AffiliateController::class, 'affiliateMakesReferral'], 20, 1);
Hooks::addAction('affwp_set_referral_status', [AffiliateController::class, 'affiliatesReferralSpecificTypeRejected'], 99, 3);
Hooks::addAction('affwp_set_referral_status', [AffiliateController::class, 'affiliatesReferralSpecificTypePaid'], 99, 3);
