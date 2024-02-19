<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Http\Services\Triggers\Groundhogg\GroundhoggController;

Route::get('groundhogg/get', [GroundhoggController::class, 'getAll']);
Route::post('groundhogg/get/form', [GroundhoggController::class, 'getFormFields']);
Route::get('groundhogg/get/tags', [GroundhoggController::class, 'getAllTags']);
