<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\Livestorm\LivestormController;
use BitCode\BTCBI\Core\Util\Route;


Route::post('livestorm_authentication', [LivestormController::class, 'authentication']);
Route::post('livestorm_fetch_all_events', [LivestormController::class, 'getAllEvents']);
Route::post('livestorm_fetch_all_sessions', [LivestormController::class, 'getAllSessions']);
