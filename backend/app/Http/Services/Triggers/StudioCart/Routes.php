<?php

if (!defined('ABSPATH')) {
    exit;
}
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;
use BitApps\BTCBI\Http\Services\Triggers\StudioCart\StudioCartController;

Route::get('studiocart/get', [StudioCartController::class, 'getAll']);
Route::post('studiocart/get/form', [StudioCartController::class, 'get_a_form']);
