<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\Notion\NotionController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('notion_authorization', [NotionController::class, 'authorization']);
Route::post('notion_database_lists', [NotionController::class, 'getAllDatabaseLists']);
Route::post('notion_database_properties', [NotionController::class, 'getFieldsProperties']);

