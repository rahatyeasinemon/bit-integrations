<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\Mailify\MailifyController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('mailify_authorize', [MailifyController::class, 'authorization']);
Route::post('mailify_lists', [MailifyController::class, 'getAllList']);
Route::post('mailify_headers', [MailifyController::class, 'mailifyHeaders']);
