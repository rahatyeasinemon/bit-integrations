<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Notion\NotionController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('notion_authorization', [NotionController::class, 'authorization']);
Route::post('notion_database_lists', [NotionController::class, 'getAllDatabaseLists']);
Route::post('notion_database_properties', [NotionController::class, 'getFieldsProperties']);
