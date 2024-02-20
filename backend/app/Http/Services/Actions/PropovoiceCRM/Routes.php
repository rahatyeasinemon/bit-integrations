<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\PropovoiceCRM\PropovoiceCRMController;
use BitApps\BTCBI\Util\Route;

Route::post('propovoice_authorize', [PropovoiceCRMController::class, 'authorizePropovoiceCrm']);
Route::post('propovoice_crm_lead_tags', [PropovoiceCRMController::class, 'leadTags']);
Route::post('propovoice_crm_lead_label', [PropovoiceCRMController::class, 'leadLabel']);
