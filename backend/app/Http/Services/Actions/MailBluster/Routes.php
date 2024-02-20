<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\MailBluster\MailBlusterController;
use BitApps\BTCBI\Util\Route;

Route::post('mailBluster_authentication', [MailBlusterController::class, 'authentication']);
