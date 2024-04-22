<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\Gravitec\GravitecController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('gravitec_authentication', [GravitecController::class, 'authentication']);
