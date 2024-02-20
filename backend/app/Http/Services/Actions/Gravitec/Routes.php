<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Gravitec\GravitecController;
use BitApps\BTCBI\Util\Route;

Route::post('gravitec_authentication', [GravitecController::class, 'authentication']);
