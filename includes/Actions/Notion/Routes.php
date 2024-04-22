<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\Notion\NotionController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('notion_authorization', [NotionController::class, 'authorization']);
Route::post('notion_database_lists', [NotionController::class, 'getAllDatabaseLists']);
Route::post('notion_database_properties', [NotionController::class, 'getFieldsProperties']);
