<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\BitForm\BitFormController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('bitForm_authorization_and_fetch_form_list', [ BitFormController::class, 'bitFormAuthorization']);
Route::post('bitForm_all_form_list', [ BitFormController::class, 'bitFormAllFormList']);
Route::post('bitForm_fetch_single_form_fields', [BitFormController::class, 'bitFormFetchSingleFormFields']);
