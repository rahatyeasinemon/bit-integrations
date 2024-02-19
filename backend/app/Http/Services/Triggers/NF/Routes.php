<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Http\Services\Triggers\NF\NFController;

Route::get('nf/get', [NFController::class, 'getAll']);
Route::post('nf/get/form', [NFController::class, 'getAForm']);
