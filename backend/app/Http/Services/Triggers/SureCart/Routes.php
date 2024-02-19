<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Triggers\SureCart\SureCartController;

Route::get('surecart/get', [SureCartController::class, 'getAll']);
Route::post('surecart/get/form', [SureCartController::class, 'get_a_form']);

Route::get('get_sureCart_all_product', [SureCartController::class, 'get_sureCart_all_product']);
