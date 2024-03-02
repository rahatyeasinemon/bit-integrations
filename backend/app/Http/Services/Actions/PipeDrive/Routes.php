<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\PipeDrive\PipeDriveController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('PipeDrive_refresh_fields', [PipeDriveController::class, 'getFields']);
Route::post('PipeDrive_fetch_meta_data', [PipeDriveController::class, 'getMetaData']);
