<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\WebHooks\WebHooksController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('test_webhook', [WebHooksController::class, 'testWebhook']);