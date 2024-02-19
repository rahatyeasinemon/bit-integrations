<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\Woodpecker\WoodpeckerController;
use BitCode\BTCBI\Util\Route;

Route::post('woodpecker_authentication', [WoodpeckerController::class, 'authentication']);
Route::post('woodpecker_fetch_all_campaigns', [WoodpeckerController::class, 'getAllCampagns']);
