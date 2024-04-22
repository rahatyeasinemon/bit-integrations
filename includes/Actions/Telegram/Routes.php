<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\Telegram\TelegramController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('telegram_authorize', [ TelegramController::class, 'telegramAuthorize']);
Route::post('refresh_get_updates', [ TelegramController::class, 'refreshGetUpdates']);
