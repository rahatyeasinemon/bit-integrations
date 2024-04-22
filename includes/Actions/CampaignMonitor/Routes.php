<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\CampaignMonitor\CampaignMonitorController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('campaign_monitor_authorize', [CampaignMonitorController::class, 'authorization']);
Route::post('campaign_monitor_lists', [CampaignMonitorController::class, 'getAllLists']);
Route::post('campaign_monitor_custom_fields', [CampaignMonitorController::class, 'getCustomFields']);
