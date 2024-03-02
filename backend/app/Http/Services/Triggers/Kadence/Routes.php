<?php

if (!defined('ABSPATH')) {
    exit;
}
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;
use BitApps\BTCBI\Http\Services\Triggers\Kadence\KadenceController;

Route::get('kadence/get', [KadenceController::class, 'getAll']);
Route::post('kadence/get/form', [KadenceController::class, 'get_a_form']);
