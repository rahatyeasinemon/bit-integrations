<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\ActiveCampaign\ActiveCampaignController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('aCampaign_authorize', [ActiveCampaignController::class, 'activeCampaignAuthorize']);
Route::post('aCampaign_headers', [ActiveCampaignController::class, 'activeCampaignHeaders']);
Route::post('aCampaign_lists', [ActiveCampaignController::class, 'activeCampaignLists']);
Route::post('aCampaign_accounts', [ActiveCampaignController::class, 'activeCampaignAccounts']);
Route::post('aCampaign_tags', [ActiveCampaignController::class, 'activeCampaignTags']);
