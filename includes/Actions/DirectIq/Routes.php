<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\DirectIq\DirectIqController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('directIq_authorize', [DirectIqController::class, 'directIqAuthorize']);
Route::post('directIq_headers', [DirectIqController::class, 'directIqHeaders']);
Route::post('directIq_lists', [DirectIqController::class, 'directIqLists']);
