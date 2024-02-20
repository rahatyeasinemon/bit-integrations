<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\SuiteDash\SuiteDashController;
use BitApps\BTCBI\Util\Route;

Route::post('suite_dash_authentication', [SuiteDashController::class, 'authentication']);
Route::post('suite_dash_fetch_all_fields', [SuiteDashController::class, 'getAllFields']);
Route::post('suite_dash_fetch_all_companies', [SuiteDashController::class, 'getAllCompanies']);
