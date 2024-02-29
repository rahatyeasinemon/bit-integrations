<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\OmniSend\OmniSendController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('Omnisend_authorization', [OmniSendController::class, 'authorization']);
