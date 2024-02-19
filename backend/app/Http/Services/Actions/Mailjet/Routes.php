<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\Mailjet\MailjetController;
use BitCode\BTCBI\Util\Route;

Route::post('mailjet_authentication', [MailjetController::class, 'authentication']);
Route::post('mailjet_fetch_all_custom_fields', [MailjetController::class, 'getCustomFields']);
