<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\Lemlist\LemlistController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('lemlist_authorize', [LemlistController::class, 'authorization']);
Route::post('lemlist_campaigns', [LemlistController::class, 'getAllCampaign']);
