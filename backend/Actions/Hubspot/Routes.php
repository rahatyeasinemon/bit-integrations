<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\Hubspot\HubspotController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('hubSpot_authorization', [HubspotController::class, 'authorization']);
Route::post('getFields', [HubspotController::class, 'getFields']);
Route::post('hubspot_pipeline', [HubspotController::class, 'getAllPipelines']);
Route::post('hubspot_owners', [HubspotController::class, 'getAllOwners']);
Route::post('hubspot_contacts', [HubspotController::class, 'getAllContacts']);
Route::post('hubspot_company', [HubspotController::class, 'getAllCompany']);
