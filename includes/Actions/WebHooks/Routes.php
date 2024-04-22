<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\WebHooks\WebHooksController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('test_webhook', [WebHooksController::class, 'testWebhook']);
