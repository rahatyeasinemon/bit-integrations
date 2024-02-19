<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\CampaignMonitor\CampaignMonitorController;
use BitCode\BTCBI\Util\Route;

Route::post('campaign_monitor_authorize', [CampaignMonitorController::class, 'authorization']);
Route::post('campaign_monitor_lists', [CampaignMonitorController::class, 'getAllLists']);
Route::post('campaign_monitor_custom_fields', [CampaignMonitorController::class, 'getCustomFields']);
