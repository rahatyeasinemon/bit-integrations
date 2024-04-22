<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\Encharge\EnchargeController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('encharge_authorize', [EnchargeController::class, 'enChargeAuthorize']);
Route::post('encharge_headers', [EnchargeController::class, 'enchargeHeaders']);
