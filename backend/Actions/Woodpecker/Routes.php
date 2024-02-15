<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\Woodpecker\WoodpeckerController;
use BitCode\BTCBI\Core\Util\Route;


Route::post('woodpecker_authentication', [WoodpeckerController::class, 'authentication']);
Route::post('woodpecker_fetch_all_campaigns', [WoodpeckerController::class, 'getAllCampagns']);