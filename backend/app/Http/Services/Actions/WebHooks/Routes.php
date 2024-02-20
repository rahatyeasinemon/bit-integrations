<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\WebHooks\WebHooksController;
use BitApps\BTCBI\Util\Route;

Route::post('test_webhook', [WebHooksController::class, 'testWebhook']);
