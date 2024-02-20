<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\MailerLite\MailerLiteController;
use BitApps\BTCBI\Util\Route;

Route::post('mailerlite_fetch_all_groups', [MailerLiteController::class, 'fetchAllGroups']);
Route::post('mailerlite_refresh_fields', [MailerLiteController::class, 'mailerliteRefreshFields']);
