<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\OneDrive\OneDriveController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('oneDrive_authorization', [OneDriveController::class, 'authorization']);
Route::post('oneDrive_get_all_folders', [OneDriveController::class, 'getAllFolders']);
Route::post('oneDrive_get_single_folder', [OneDriveController::class, 'singleOneDriveFolderList']);
