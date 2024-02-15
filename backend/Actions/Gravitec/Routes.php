<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\Gravitec\GravitecController;
use BitCode\BTCBI\Core\Util\Route;


Route::post('gravitec_authentication', [GravitecController::class, 'authentication']);
