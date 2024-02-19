<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\PipeDrive\PipeDriveController;
use BitCode\BTCBI\Util\Route;

Route::post('PipeDrive_refresh_fields', [PipeDriveController::class, 'getFields']);
Route::post('PipeDrive_fetch_meta_data', [PipeDriveController::class, 'getMetaData']);
