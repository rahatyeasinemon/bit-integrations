<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\Encharge\EnchargeController;
use BitCode\BTCBI\Util\Route;

Route::post('encharge_authorize', [EnchargeController::class, 'enChargeAuthorize']);
Route::post('encharge_headers', [EnchargeController::class, 'enchargeHeaders']);
