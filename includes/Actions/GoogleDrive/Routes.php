<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\GoogleDrive\GoogleDriveController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('googleDrive_authorization', [GoogleDriveController::class, 'authorization']);
Route::post('googleDrive_get_all_folders', [GoogleDriveController::class, 'getAllFolders']);
