<?php


if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Core\Util\Route;
use BitCode\BTCBI\Triggers\Webhook\WebhookController;

Route::get('webhook/new', [WebhookController::class, 'getNewHook']);
Route::post('webhook/test', [WebhookController::class, 'getTestData']);
Route::post('webhook/test/remove', [WebhookController::class, 'removeTestData']);