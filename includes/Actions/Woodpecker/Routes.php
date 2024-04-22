<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\Woodpecker\WoodpeckerController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('woodpecker_authentication', [WoodpeckerController::class, 'authentication']);
Route::post('woodpecker_fetch_all_campaigns', [WoodpeckerController::class, 'getAllCampagns']);
