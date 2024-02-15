<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Core\Util\Route;
use BitCode\BTCBI\Triggers\Groundhogg\GroundhoggController;

Route::get('groundhogg/get', [GroundhoggController::class, 'getAll']);
Route::post('groundhogg/get/form', [GroundhoggController::class, 'getFormFields']);
Route::get('groundhogg/get/tags', [GroundhoggController::class, 'getAllTags']);
