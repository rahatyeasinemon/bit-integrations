<?php

if (!defined('ABSPATH')) {
    exit;
}
use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Http\Services\Triggers\WPF\WPFController;

Route::get('wpf/get', [WPFController::class, 'getAll']);
Route::post('wpf/get/form', [WPFController::class, 'get_a_form']);
