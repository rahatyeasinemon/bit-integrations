<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\EmailOctopus\EmailOctopusController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('emailOctopus_authentication', [EmailOctopusController::class, 'authentication']);
Route::post('emailOctopus_fetch_all_tags', [EmailOctopusController::class, 'getAllTags']);
Route::post('emailOctopus_fetch_all_fields', [EmailOctopusController::class, 'getAllFields']);
