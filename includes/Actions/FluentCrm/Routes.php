<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\FluentCrm\FluentCrmController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('fluent_crm_authorize', [FluentCrmController::class, 'fluentCrmAuthorize']);
Route::post('refresh_fluent_crm_lists', [FluentCrmController::class, 'fluentCrmLists']);
Route::post('refresh_fluent_crm_tags', [FluentCrmController::class, 'fluentCrmTags']);
Route::post('fluent_crm_headers', [FluentCrmController::class, 'fluentCrmFields']);
