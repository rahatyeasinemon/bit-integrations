<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\Smaily\SmailyController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('smaily_authentication', [SmailyController::class, 'authentication']);
