<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Gravitec\GravitecController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('gravitec_authentication', [GravitecController::class, 'authentication']);
