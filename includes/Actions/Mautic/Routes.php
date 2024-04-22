<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\Mautic\MauticController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('mautic_generate_token', [ MauticController::class, 'generateTokens']);
// Route::post('mChimp_refresh_audience', [ MailChimpController::class, 'refreshAudience']);
Route::post('mautic_get_fields', [ MauticController::class, 'getAllFields']);
Route::post('mautic_get_tags', [ MauticController::class, 'getAllTags']);
