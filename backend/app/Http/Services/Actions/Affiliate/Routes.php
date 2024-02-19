<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\Affiliate\AffiliateController;
use BitCode\BTCBI\Util\Route;

Route::post('affiliate_authorize', [AffiliateController::class, 'authorizeAffiliate']);
Route::post('affiliate_fetch_all_affiliate', [AffiliateController::class, 'getAllAffiliate']);
