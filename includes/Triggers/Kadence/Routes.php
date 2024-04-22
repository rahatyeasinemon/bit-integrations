<?php

if (!defined('ABSPATH')) {
    exit;
}
use BitApps\BTCBI_PRO\Core\Util\Route;
use BitApps\BTCBI_PRO\Triggers\Kadence\KadenceController;

Route::get('kadence/get', [KadenceController::class, 'getAll']);
Route::post('kadence/get/form', [KadenceController::class, 'get_a_form']);
