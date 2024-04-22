<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\SendPulse\SendPulseController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('sendPulse_authorize', [SendPulseController::class, 'authorization']);
Route::post('sendPulse_lists', [SendPulseController::class, 'getAllList']);
Route::post('sendPulse_headers', [SendPulseController::class, 'sendPulseHeaders']);
