<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\Mautic\MauticController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('mautic_generate_token', [ MauticController::class, 'generateTokens']);
// Route::post('mChimp_refresh_audience', [ MailChimpController::class, 'refreshAudience']);
Route::post('mautic_get_fields', [ MauticController::class, 'getAllFields']);
Route::post('mautic_get_tags', [ MauticController::class, 'getAllTags']);
