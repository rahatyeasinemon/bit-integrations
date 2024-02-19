<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\Lemlist\LemlistController;
use BitCode\BTCBI\Util\Route;

Route::post('lemlist_authorize', [LemlistController::class, 'authorization']);
Route::post('lemlist_campaigns', [LemlistController::class, 'getAllCampaign']);
