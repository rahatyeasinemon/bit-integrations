<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Woodpecker\WoodpeckerController;
use BitApps\BTCBI\Util\Route;

Route::post('woodpecker_authentication', [WoodpeckerController::class, 'authentication']);
Route::post('woodpecker_fetch_all_campaigns', [WoodpeckerController::class, 'getAllCampagns']);
