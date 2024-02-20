<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\SystemeIO\SystemeIOController;
use BitApps\BTCBI\Util\Route;

Route::post('systemeIO_authentication', [SystemeIOController::class, 'authentication']);
Route::post('systemeIO_fetch_all_tags', [SystemeIOController::class, 'getAllTags']);
