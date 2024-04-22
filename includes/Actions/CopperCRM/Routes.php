<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\CopperCRM\CopperCRMController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('coppercrm_authentication', [CopperCRMController::class, 'authentication']);
Route::post('coppercrm_fetch_custom_fields', [CopperCRMController::class, 'getCustomFields']);
Route::post('coppercrm_fetch_all_opportunities', [CopperCRMController::class, 'getAllOpportunities']);
Route::post('coppercrm_fetch_all_owners', [CopperCRMController::class, 'getAllOwners']);
Route::post('coppercrm_fetch_all_companies', [CopperCRMController::class, 'getAllCompanies']);
Route::post('coppercrm_fetch_all_pipelineStages', [CopperCRMController::class, 'getAllPipelineStages']);
Route::post('coppercrm_fetch_all_CRMPeoples', [CopperCRMController::class, 'getAllCRMPeoples']);
Route::post('coppercrm_fetch_all_CRMPipelines', [CopperCRMController::class, 'getAllCRMPipelines']);
