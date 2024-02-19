<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\Rapidmail\RapidmailController;
use BitCode\BTCBI\Util\Route;

//Rapidmail
Route::post('rapidmail_authorization', [RapidmailController::class, 'checkAuthorization']);
Route::get('rapidmail_get_all_recipients', [RapidmailController::class, 'getAllRecipients']);
Route::get('rapidmail_get_all_fields', [RapidmailController::class, 'getAllFields']);
