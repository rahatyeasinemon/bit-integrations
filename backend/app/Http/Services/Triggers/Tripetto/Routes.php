<?php

if (!defined('ABSPATH')) {
    exit;
}
use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Http\Services\Triggers\Tripetto\TripettoController;

Route::get('tripetto/get', [TripettoController::class, 'getAll']);
Route::post('tripetto/get/form', [TripettoController::class, 'get_a_form']);
