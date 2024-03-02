<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;
use BitApps\BTCBI\Http\Services\Triggers\EDD\EDDController;

Route::get('edd/get', [EDDController::class, 'getAll']);
Route::post('edd/get/form', [EDDController::class, 'get_a_form']);

Route::get('get_edd_all_product', [EDDController::class, 'getProduct']);
Route::get('get_edd_all_discount_code', [EDDController::class, 'getDiscount']);
