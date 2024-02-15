<?php
if (!defined('ABSPATH')) {
    exit;
}
use BitCode\BTCBI\Core\Util\Route;
use BitCode\BTCBI\Triggers\StudioCart\StudioCartController;

Route::get('studiocart/get', [StudioCartController::class, 'getAll']);
Route::post('studiocart/get/form', [StudioCartController::class, 'get_a_form']);