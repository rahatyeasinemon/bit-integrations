<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Salesflare\SalesflareController;
use BitApps\BTCBI\Util\Route;

Route::post('salesflare_authentication', [SalesflareController::class, 'authentication']);
Route::post('Salesflare_custom_fields', [SalesflareController::class, 'customFields']);
Route::post('Salesflare_fetch_all_tags', [SalesflareController::class, 'getAllTags']);
Route::post('Salesflare_fetch_all_account', [SalesflareController::class, 'getAllAccounts']);
Route::post('Salesflare_fetch_all_pipelines', [SalesflareController::class, 'getAllPipelines']);
