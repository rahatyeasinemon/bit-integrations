<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\LionDesk\LionDeskController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('lionDesk_generate_token', [LionDeskController::class, 'generateTokens']);
Route::post('lionDesk_fetch_custom_fields', [LionDeskController::class, 'getCustomFields']);
Route::post('lionDesk_fetch_all_tags', [LionDeskController::class, 'getAllTags']);
