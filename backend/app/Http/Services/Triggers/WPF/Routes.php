<?php

if (!defined('ABSPATH')) {
    exit;
}
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;
use BitApps\BTCBI\Http\Services\Triggers\WPF\WPFController;

Route::get('wpf/get', [WPFController::class, 'getAll']);
Route::post('wpf/get/form', [WPFController::class, 'get_a_form']);
