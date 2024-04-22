<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\Dropbox\DropboxController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('dropbox_authorization', [DropboxController::class, 'checkAuthorization']);
Route::post('dropbox_get_all_folders', [DropboxController::class, 'getAllFolders']);
