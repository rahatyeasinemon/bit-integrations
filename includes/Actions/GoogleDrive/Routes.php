<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\GoogleDrive\GoogleDriveController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('googleDrive_authorization', [GoogleDriveController::class, 'authorization']);
Route::post('googleDrive_get_all_folders', [GoogleDriveController::class, 'getAllFolders']);
