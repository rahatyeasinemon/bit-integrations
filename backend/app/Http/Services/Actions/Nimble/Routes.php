<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Nimble\NimbleController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('nimble_authentication', [NimbleController::class, 'authentication']);
Route::post('nimble_fetch_all_fields', [NimbleController::class, 'getAllFields']);
Route::post('nimble_fetch_all_sessions', [NimbleController::class, 'getAllSessions']);
