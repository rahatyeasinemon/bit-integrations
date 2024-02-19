<?php


if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Http\Services\Triggers\ActionHook\ActionHookController;

Route::post('action_hook/test', [ActionHookController::class, 'getTestData']);
Route::post('action_hook/test/remove', [ActionHookController::class, 'removeTestData']);
