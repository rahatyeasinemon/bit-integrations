<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Core\Util\Route;
use BitCode\BTCBI\Triggers\NF\NFController;

Route::get('nf/get', [NFController::class, 'getAll']);
Route::post('nf/get/form', [NFController::class, 'getAForm']);
