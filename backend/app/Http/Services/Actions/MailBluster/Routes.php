<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\MailBluster\MailBlusterController;
use BitCode\BTCBI\Util\Route;

Route::post('mailBluster_authentication', [MailBlusterController::class, 'authentication']);
