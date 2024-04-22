<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\Mailercloud\MailercloudController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('mailercloud_handle_authorize', [MailercloudController::class, 'handleAuthorize']);
Route::post('mailercloud_get_all_lists', [MailercloudController::class, 'getAllLists']);
Route::post('mailercloud_get_all_fields', [MailercloudController::class, 'getAllFields']);
