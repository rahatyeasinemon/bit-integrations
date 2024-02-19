<?php

if (!defined('ABSPATH')) {
    exit;
}
use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Triggers\Kadence\KadenceController;

Route::get('kadence/get', [KadenceController::class, 'getAll']);
Route::post('kadence/get/form', [KadenceController::class, 'get_a_form']);
