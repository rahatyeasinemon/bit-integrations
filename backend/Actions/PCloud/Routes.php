<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\PCloud\PCloudController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('pCloud_authorization', [PCloudController::class, 'authorization']);
Route::post('pCloud_get_all_folders', [PCloudController::class, 'getAllFolders']);
