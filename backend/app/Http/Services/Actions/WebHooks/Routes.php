<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\WebHooks\WebHooksController;
use BitCode\BTCBI\Util\Route;

Route::post('test_webhook', [WebHooksController::class, 'testWebhook']);
