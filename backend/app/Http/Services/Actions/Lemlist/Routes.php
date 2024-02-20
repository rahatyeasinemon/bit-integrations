<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Lemlist\LemlistController;
use BitApps\BTCBI\Util\Route;

Route::post('lemlist_authorize', [LemlistController::class, 'authorization']);
Route::post('lemlist_campaigns', [LemlistController::class, 'getAllCampaign']);
