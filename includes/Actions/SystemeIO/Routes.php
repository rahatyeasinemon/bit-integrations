<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\SystemeIO\SystemeIOController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('systemeIO_authentication', [SystemeIOController::class, 'authentication']);
Route::post('systemeIO_fetch_all_tags', [SystemeIOController::class, 'getAllTags']);
