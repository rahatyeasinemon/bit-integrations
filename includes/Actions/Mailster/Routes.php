<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\FI\Actions\Mailster\MailsterController;
use BitCode\FI\Core\Util\Route;

Route::post('mailster_authentication', [MailsterController::class, 'authentication']);
Route::post('mailster_fields', [MailsterController::class, 'getMailsterFields']);
