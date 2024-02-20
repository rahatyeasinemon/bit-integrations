<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Dropbox\DropboxController;
use BitApps\BTCBI\Util\Route;

Route::post('dropbox_authorization', [DropboxController::class, 'checkAuthorization']);
Route::post('dropbox_get_all_folders', [DropboxController::class, 'getAllFolders']);
