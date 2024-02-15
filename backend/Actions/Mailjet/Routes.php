<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\Mailjet\MailjetController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('mailjet_authentication', [MailjetController::class, 'authentication']);
Route::post('mailjet_fetch_all_custom_fields', [MailjetController::class, 'getCustomFields']);
