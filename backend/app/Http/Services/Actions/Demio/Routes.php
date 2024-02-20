<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Demio\DemioController;
use BitApps\BTCBI\Util\Route;

Route::post('demio_authentication', [DemioController::class, 'authentication']);
Route::post('demio_fetch_all_events', [DemioController::class, 'getAllEvents']);
Route::post('demio_fetch_all_sessions', [DemioController::class, 'getAllSessions']);
