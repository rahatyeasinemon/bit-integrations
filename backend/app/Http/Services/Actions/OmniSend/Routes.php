<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\OmniSend\OmniSendController;
use BitApps\BTCBI\Util\Route;

Route::post('Omnisend_authorization', [OmniSendController::class, 'authorization']);
