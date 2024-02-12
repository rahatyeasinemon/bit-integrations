<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Core\Util\Route;
use BitCode\BTCBI\Triggers\CustomTrigger\CustomTriggerController;

Route::get('custom_trigger/new', [CustomTriggerController::class, 'getNewHook']);
Route::post('custom_trigger/test', [CustomTriggerController::class, 'getTestData']);
Route::post('custom_trigger/test/remove', [CustomTriggerController::class, 'removeTestData']);
