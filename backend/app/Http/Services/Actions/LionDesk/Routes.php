<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\LionDesk\LionDeskController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('lionDesk_generate_token', [LionDeskController::class, 'generateTokens']);
Route::post('lionDesk_fetch_custom_fields', [LionDeskController::class, 'getCustomFields']);
Route::post('lionDesk_fetch_all_tags', [LionDeskController::class, 'getAllTags']);
