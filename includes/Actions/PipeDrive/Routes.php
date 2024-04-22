<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\PipeDrive\PipeDriveController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('PipeDrive_refresh_fields', [PipeDriveController::class, 'getFields']);
Route::post('PipeDrive_fetch_meta_data', [PipeDriveController::class, 'getMetaData']);
