<?php


if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;
use BitApps\BTCBI\Http\Services\Triggers\ActionHook\ActionHookController;

Route::post('action_hook/test', [ActionHookController::class, 'getTestData']);
Route::post('action_hook/test/remove', [ActionHookController::class, 'removeTestData']);
