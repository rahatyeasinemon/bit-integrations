<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\ZohoCRM\ZohoCRMController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('zcrm_get_users', [ZohoCRMController::class, 'refreshUsersAjaxHelper']);
Route::post('zcrm_get_tags', [ZohoCRMController::class, 'refreshTagListAjaxHelper']);
Route::post('zcrm_get_assignment_rules', [ZohoCRMController::class, 'getAssignmentRulesAjaxHelper']);
Route::post('zcrm_get_related_lists', [ZohoCRMController::class, 'getRelatedListsAjaxHelper']);
Route::post('zcrm_generate_token', [ZohoCRMController::class, 'generateTokens']);
Route::post('zcrm_refresh_modules', [ZohoCRMController::class, 'refreshModulesAjaxHelper']);
Route::post('zcrm_refresh_layouts', [ZohoCRMController::class, 'refreshLayoutsAjaxHelper']);

//Rapidmail
// Route::post('rapidmail_authorization', [RapidmailController::class, 'checkAuthorization']);
