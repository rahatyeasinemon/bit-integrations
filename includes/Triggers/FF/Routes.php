<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Core\Util\Route;
use BitCode\BTCBI\Triggers\FF\FFController;

Route::get('ff/get', [FFController::class, 'getAll']);
Route::post('ff/get/form', [FFController::class, 'get_a_form']);