<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\ZohoCampaigns\ZohoCampaignsController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('zcampaigns_generate_token', [ZohoCampaignsController::class, 'generateTokens']);
Route::post('zcampaigns_refresh_lists', [ZohoCampaignsController::class, 'refreshLists']);
Route::post('zcampaigns_refresh_contact_fields', [ZohoCampaignsController::class, 'refreshContactFields']);
