<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\Mailup\MailupController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('mailup_authorization', [MailupController::class, 'authorization']);
Route::post('mailup_fetch_all_list', [MailupController::class, 'getAllList']);
Route::post('mailup_fetch_all_group', [MailupController::class, 'getAllGroup']);
