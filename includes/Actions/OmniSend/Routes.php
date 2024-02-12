<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\OmniSend\OmniSendController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('Omnisend_authorization', [OmniSendController::class, 'authorization']);
