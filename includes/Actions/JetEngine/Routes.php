<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\FI\Actions\JetEngine\JetEngineController;
use BitCode\FI\Core\Util\Route;

Route::post('jetEngine_authentication', [JetEngineController::class, 'authentication']);
Route::post('jetEngine_fetch_eu_fields', [JetEngineController::class, 'getEUFields']);
Route::post('jetEngine_fetch_vendors', [JetEngineController::class, 'getAllVendors']);
