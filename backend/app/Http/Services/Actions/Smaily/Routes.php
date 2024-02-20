<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Smaily\SmailyController;
use BitApps\BTCBI\Util\Route;

Route::post('smaily_authentication', [SmailyController::class, 'authentication']);
