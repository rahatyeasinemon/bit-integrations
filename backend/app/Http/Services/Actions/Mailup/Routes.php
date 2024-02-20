<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Mailup\MailupController;
use BitApps\BTCBI\Util\Route;

Route::post('mailup_authorization', [MailupController::class, 'authorization']);
Route::post('mailup_fetch_all_list', [MailupController::class, 'getAllList']);
Route::post('mailup_fetch_all_group', [MailupController::class, 'getAllGroup']);
