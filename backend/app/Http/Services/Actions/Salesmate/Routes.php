<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Salesmate\SalesmateController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('salesmate_authentication', [SalesmateController::class, 'authentication']);
Route::post('Salesmate_fields', [SalesmateController::class, 'getAllFields']);
Route::post('salesmate_fetch_all_CRMSources', [SalesmateController::class, 'getAllCRMSources']);
Route::post('salesmate_fetch_all_currencies', [SalesmateController::class, 'getAllCurrencies']);
Route::post('salesmate_fetch_all_tags', [SalesmateController::class, 'getAllTags']);
Route::post('salesmate_fetch_all_CRMPipelines', [SalesmateController::class, 'getAllCRMPipelines']);
Route::post('salesmate_fetch_all_CRMOwners', [SalesmateController::class, 'getAllCRMOwners']);
Route::post('salesmate_fetch_all_CRMContacts', [SalesmateController::class, 'getAllCRMContacts']);
Route::post('salesmate_fetch_all_CRMCompanies', [SalesmateController::class, 'getAllCRMCompanies']);
