<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\FI\Actions\Woodpecker\WoodpeckerController;
use BitCode\FI\Core\Util\Route;


Route::post('woodpecker_authentication', [WoodpeckerController::class, 'authentication']);
Route::post('woodpecker_custom_fields', [WoodpeckerController::class, 'customFields']);
Route::post('woodpecker_fetch_all_tags', [WoodpeckerController::class, 'getAllTags']);
Route::post('woodpecker_fetch_all_account', [WoodpeckerController::class, 'getAllAccounts']);
Route::post('woodpecker_fetch_all_pipelines', [WoodpeckerController::class, 'getAllPipelines']);
