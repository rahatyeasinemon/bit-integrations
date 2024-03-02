<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;
use BitApps\BTCBI\Http\Services\Triggers\CustomTrigger\CustomTriggerController;

Route::get('custom_trigger/new', [CustomTriggerController::class, 'getNewHook']);
Route::post('custom_trigger/test', [CustomTriggerController::class, 'getTestData']);
Route::post('custom_trigger/test/remove', [CustomTriggerController::class, 'removeTestData']);
