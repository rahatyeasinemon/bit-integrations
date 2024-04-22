<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\Vbout\VboutController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('vbout_handle_authorize', [VboutController::class, 'handleAuthorize']);
Route::post('vbout_fetch_all_lists', [VboutController::class, 'fetchAllLists']);
Route::post('vbout_refresh_fields', [VboutController::class, 'vboutRefreshFields']);
