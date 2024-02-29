<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Smaily\SmailyController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('smaily_authentication', [SmailyController::class, 'authentication']);
