<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\Gravitec\GravitecController;
use BitCode\BTCBI\Util\Route;

Route::post('gravitec_authentication', [GravitecController::class, 'authentication']);
