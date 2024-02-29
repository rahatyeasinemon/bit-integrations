<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\DirectIq\DirectIqController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('directIq_authorize', [DirectIqController::class, 'directIqAuthorize']);
Route::post('directIq_headers', [DirectIqController::class, 'directIqHeaders']);
Route::post('directIq_lists', [DirectIqController::class, 'directIqLists']);
