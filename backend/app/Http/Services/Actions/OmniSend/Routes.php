<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\OmniSend\OmniSendController;
use BitCode\BTCBI\Util\Route;

Route::post('Omnisend_authorization', [OmniSendController::class, 'authorization']);
