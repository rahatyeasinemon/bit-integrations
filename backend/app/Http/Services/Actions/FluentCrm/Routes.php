<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\FluentCrm\FluentCrmController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('fluent_crm_authorize', [FluentCrmController::class, 'fluentCrmAuthorize']);
Route::post('refresh_fluent_crm_lists', [FluentCrmController::class, 'fluentCrmLists']);
Route::post('refresh_fluent_crm_tags', [FluentCrmController::class, 'fluentCrmTags']);
Route::post('fluent_crm_headers', [FluentCrmController::class, 'fluentCrmFields']);
