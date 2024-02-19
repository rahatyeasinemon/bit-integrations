<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\MailerLite\MailerLiteController;
use BitCode\BTCBI\Util\Route;

Route::post('mailerlite_fetch_all_groups', [MailerLiteController::class, 'fetchAllGroups']);
Route::post('mailerlite_refresh_fields', [MailerLiteController::class, 'mailerliteRefreshFields']);
