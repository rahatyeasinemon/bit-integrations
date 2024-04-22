<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\OmniSend\OmniSendController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('Omnisend_authorization', [OmniSendController::class, 'authorization']);
