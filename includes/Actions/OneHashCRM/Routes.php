<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\FI\Actions\OneHashCRM\OneHashCRMController;
use BitCode\FI\Core\Util\Route;


Route::post('onehashcrm_authentication', [OneHashCRMController::class, 'authentication']);
Route::post('onehashcrm_custom_fields', [OneHashCRMController::class, 'getCustomFields']);
Route::post('onehashcrm_fetch_all_customers', [OneHashCRMController::class, 'getAllCustomer']);
Route::post('onehashcrm_fetch_all_leads', [OneHashCRMController::class, 'getAllLead']);
Route::post('onehashcrm_fetch_all_staffs', [OneHashCRMController::class, 'getAllStaff']);
