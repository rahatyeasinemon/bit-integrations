<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\PCloud\PCloudController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('pCloud_authorization', [PCloudController::class, 'authorization']);
Route::post('pCloud_get_all_folders', [PCloudController::class, 'getAllFolders']);
