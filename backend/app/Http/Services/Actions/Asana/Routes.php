<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Asana\AsanaController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('asana_authentication', [AsanaController::class, 'authentication']);
Route::post('asana_fetch_custom_fields', [AsanaController::class, 'getCustomFields']);
Route::post('asana_fetch_all_tasks', [AsanaController::class, 'getAllTasks']);
Route::post('asana_fetch_all_Projects', [AsanaController::class, 'getAllProjects']);
Route::post('asana_fetch_all_Sections', [AsanaController::class, 'getAllSections']);
