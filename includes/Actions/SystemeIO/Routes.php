<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\SystemeIO\SystemeIOController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('systemeIO_authentication', [SystemeIOController::class, 'authentication']);
Route::post('systemeIO_fetch_all_tags', [SystemeIOController::class, 'getAllTags']);
