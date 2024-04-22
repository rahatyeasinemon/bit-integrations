<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\MailerLite\MailerLiteController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('mailerlite_fetch_all_groups', [MailerLiteController::class, 'fetchAllGroups']);
Route::post('mailerlite_refresh_fields', [MailerLiteController::class, 'mailerliteRefreshFields']);
