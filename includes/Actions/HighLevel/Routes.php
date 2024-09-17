<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\FI\Actions\HighLevel\HighLevelController;
use BitCode\FI\Core\Util\Route;

Route::post('highLevel_authorization', [HighLevelController::class, 'highLevelAuthorization']);
Route::post('get_highLevel_contact_custom_fields', [HighLevelController::class, 'getCustomFields']);
Route::post('high_level_contact_tags', [HighLevelController::class, 'getAllTags']);
Route::post('get_highLevel_contacts', [HighLevelController::class, 'getContacts']);
