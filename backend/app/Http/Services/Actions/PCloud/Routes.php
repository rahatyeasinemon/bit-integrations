<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\PCloud\PCloudController;
use BitApps\BTCBI\Util\Route;

Route::post('pCloud_authorization', [PCloudController::class, 'authorization']);
Route::post('pCloud_get_all_folders', [PCloudController::class, 'getAllFolders']);
