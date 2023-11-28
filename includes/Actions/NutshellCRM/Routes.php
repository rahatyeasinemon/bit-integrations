<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\FI\Actions\NutshellCRM\NutshellCRMController;
use BitCode\FI\Core\Util\Route;

Route::post('nutshellcrm_authentication', [NutshellCRMController::class, 'authentication']);
Route::post('nutshellcrm_fetch_all_contacts', [NutshellCRMController::class, 'getContacts']);
Route::post('nutshellcrm_fetch_all_companytypes', [NutshellCRMController::class, 'getCompanyTypes']);
