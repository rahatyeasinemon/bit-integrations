<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\MailBluster\MailBlusterController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('mailBluster_authentication', [MailBlusterController::class, 'authentication']);
