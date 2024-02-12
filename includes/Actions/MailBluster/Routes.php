<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\MailBluster\MailBlusterController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('mailBluster_authentication', [MailBlusterController::class, 'authentication']);
