<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\Smaily\SmailyController;
use BitCode\BTCBI\Util\Route;

Route::post('smaily_authentication', [SmailyController::class, 'authentication']);
