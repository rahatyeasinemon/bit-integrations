<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Route;
use BitApps\BTCBI\Http\Services\Triggers\NF\NFController;

Route::get('nf/get', [NFController::class, 'getAll']);
Route::post('nf/get/form', [NFController::class, 'getAForm']);
