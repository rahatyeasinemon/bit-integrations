<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Affiliate\AffiliateController;
use BitApps\BTCBI\Util\Route;

Route::post('affiliate_authorize', [AffiliateController::class, 'authorizeAffiliate']);
Route::post('affiliate_fetch_all_affiliate', [AffiliateController::class, 'getAllAffiliate']);
