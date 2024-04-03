<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\FI\Core\Util\Route;
use BitCode\FI\Actions\Keap\KeapController;

Route::post('keap_generate_token', [KeapController::class, 'generateTokens']);
Route::post('keap_fetch_all_tags', [KeapController::class, 'refreshTagListAjaxHelper']);
Route::post('keap_fetch_all_custom_fields', [KeapController::class, 'refreshCustomFieldAjaxHelper']);
