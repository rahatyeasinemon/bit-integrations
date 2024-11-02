<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\FI\Actions\LMFWC\LMFWCController;
use BitCode\FI\Core\Util\Route;

Route::post('lmfwc_authentication', [LMFWCController::class, 'authentication']);
Route::post('lmfwc_fetch_all_customer', [LMFWCController::class, 'getAllCustomers']);
Route::post('lmfwc_fetch_all_sessions', [LMFWCController::class, 'getAllSessions']);
