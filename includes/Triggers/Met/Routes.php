<?php
if (!defined('ABSPATH')) {
    exit;
}
use BitCode\BTCBI\Core\Util\Route;
use BitCode\BTCBI\Triggers\Met\MetController;

Route::get('met/get', [MetController::class, 'getAll']);
Route::post('met/get/form', [MetController::class, 'get_a_form']);
