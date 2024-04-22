<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\Smaily\SmailyController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('smaily_authentication', [SmailyController::class, 'authentication']);
