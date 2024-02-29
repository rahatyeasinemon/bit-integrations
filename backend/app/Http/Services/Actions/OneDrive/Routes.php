<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\OneDrive\OneDriveController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('oneDrive_authorization', [OneDriveController::class, 'authorization']);
Route::post('oneDrive_get_all_folders', [OneDriveController::class, 'getAllFolders']);
Route::post('oneDrive_get_single_folder', [OneDriveController::class, 'singleOneDriveFolderList']);
