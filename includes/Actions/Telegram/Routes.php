<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\Telegram\TelegramController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('telegram_authorize', [ TelegramController::class, 'telegramAuthorize']);
Route::post('refresh_get_updates', [ TelegramController::class, 'refreshGetUpdates']);
