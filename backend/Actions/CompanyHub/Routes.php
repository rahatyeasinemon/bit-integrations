<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\CompanyHub\CompanyHubController;
use BitCode\BTCBI\Core\Util\Route;


Route::post('company_hub_authentication', [CompanyHubController::class, 'authentication']);
Route::post('company_hub_fetch_all_contacts', [CompanyHubController::class, 'getAllContacts']);
Route::post('company_hub_fetch_all_companies', [CompanyHubController::class, 'getAllCompanies']);
