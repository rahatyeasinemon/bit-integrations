<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\MailBluster\MailBlusterController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('mailBluster_authentication', [MailBlusterController::class, 'authentication']);
