<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\DirectIq\DirectIqController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('directIq_authorize', [DirectIqController::class, 'directIqAuthorize']);
Route::post('directIq_headers', [DirectIqController::class, 'directIqHeaders']);
Route::post('directIq_lists', [DirectIqController::class, 'directIqLists']);
