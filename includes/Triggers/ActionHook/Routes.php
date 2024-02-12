<?php


if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Core\Util\Route;
use BitCode\BTCBI\Triggers\ActionHook\ActionHookController;

Route::post('action_hook/test', [ActionHookController::class, 'getTestData']);
Route::post('action_hook/test/remove', [ActionHookController::class, 'removeTestData']);
