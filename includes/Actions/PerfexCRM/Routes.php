<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\FI\Actions\PerfexCRM\PerfexCRMController;
use BitCode\FI\Core\Util\Route;


Route::post('perfexcrm_authentication', [PerfexCRMController::class, 'authentication']);
Route::post('perfexcrm_custom_fields', [PerfexCRMController::class, 'getCustomFields']);
Route::post('perfexcrm_fetch_all_customers', [PerfexCRMController::class, 'getAllCustomer']);
Route::post('perfexcrm_fetch_all_leads', [PerfexCRMController::class, 'getAllLead']);
Route::post('perfexcrm_fetch_all_staffs', [PerfexCRMController::class, 'getAllStaff']);

Route::post('perfexcrm_fetch_all_currencies', [PerfexCRMController::class, 'getAllCurrencies']);
Route::post('perfexcrm_fetch_all_tags', [PerfexCRMController::class, 'getAllTags']);
Route::post('perfexcrm_fetch_all_CRMPipelines', [PerfexCRMController::class, 'getAllCRMPipelines']);
Route::post('perfexcrm_fetch_all_CRMOwners', [PerfexCRMController::class, 'getAllCRMOwners']);
Route::post('perfexcrm_fetch_all_CRMContacts', [PerfexCRMController::class, 'getAllCRMContacts']);
Route::post('perfexcrm_fetch_all_CRMCompanies', [PerfexCRMController::class, 'getAllCRMCompanies']);
