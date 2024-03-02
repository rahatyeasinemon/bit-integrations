<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\WebHooks\WebHooksController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('test_webhook', [WebHooksController::class, 'testWebhook']);
