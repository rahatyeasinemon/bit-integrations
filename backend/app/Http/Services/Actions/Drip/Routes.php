<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\Drip\DripController;
use BitCode\BTCBI\Util\Route;

Route::post('drip_authorize', [DripController::class, 'dripAuthorize']);
Route::post('drip_headers', [DripController::class, 'dripHeaders']);
Route::post('drip_campaigns', [DripController::class, 'dripCampaigns']);
