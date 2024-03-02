<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;
use BitApps\BTCBI\Http\Services\Triggers\Groundhogg\GroundhoggController;

Route::get('groundhogg/get', [GroundhoggController::class, 'getAll']);
Route::post('groundhogg/get/form', [GroundhoggController::class, 'getFormFields']);
Route::get('groundhogg/get/tags', [GroundhoggController::class, 'getAllTags']);
