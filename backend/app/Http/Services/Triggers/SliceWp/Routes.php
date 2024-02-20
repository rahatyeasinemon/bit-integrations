<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Route;
use BitApps\BTCBI\Http\Services\Triggers\SliceWp\SliceWpController;

Route::get('slicewp/get', [SliceWpController::class, 'getAll']);
Route::post('slicewp/get/form', [SliceWpController::class, 'get_a_form']);

Route::get('get_all_commission_type', [SliceWpController::class, 'all_commission_type']);
