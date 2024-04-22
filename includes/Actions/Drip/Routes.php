<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\Drip\DripController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('drip_authorize', [DripController::class, 'dripAuthorize']);
Route::post('drip_headers', [DripController::class, 'dripHeaders']);
Route::post('drip_campaigns', [DripController::class, 'dripCampaigns']);
