<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\Dropbox\DropboxController;
use BitCode\BTCBI\Util\Route;

Route::post('dropbox_authorization', [DropboxController::class, 'checkAuthorization']);
Route::post('dropbox_get_all_folders', [DropboxController::class, 'getAllFolders']);
