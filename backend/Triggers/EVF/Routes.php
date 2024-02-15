<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Core\Util\Route;
use BitCode\BTCBI\Triggers\EVF\EVFController;

Route::get('evf/get', [EVFController::class, 'getAll']);
Route::post('evf/get/form', [EVFController::class, 'getAForm']);
