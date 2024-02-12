<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\MailChimp\MailChimpController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('mChimp_generate_token', [ MailChimpController::class, 'generateTokens']);
Route::post('mChimp_refresh_audience', [ MailChimpController::class, 'refreshAudience']);
Route::post('mChimp_refresh_fields', [ MailChimpController::class, 'refreshAudienceFields']);
Route::post('mChimp_refresh_tags', [ MailChimpController::class, 'refreshTags']);
