<?php
if (!defined('ABSPATH')) {
    exit;
}
use BitCode\BTCBI\Core\Util\Route;
use BitCode\BTCBI\Triggers\Happy\HappyController;

Route::get('happy/get', [HappyController::class, 'getAll']);
Route::post('happy/get/form', [HappyController::class, 'get_a_form']);