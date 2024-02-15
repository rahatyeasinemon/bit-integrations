<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\Encharge\EnchargeController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('encharge_authorize', [EnchargeController::class, 'enChargeAuthorize']);
Route::post('encharge_headers', [EnchargeController::class, 'enchargeHeaders']);
