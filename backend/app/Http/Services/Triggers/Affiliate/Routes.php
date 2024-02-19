<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Http\Services\Triggers\Affiliate\AffiliateController;

Route::get('affiliate/get', [AffiliateController::class, 'getAll']);
Route::post('affiliate/get/form', [AffiliateController::class, 'get_a_form']);
Route::get('affiliate_get_all_type', [AffiliateController::class, 'affiliateGetAllType']);
