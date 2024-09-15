<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\FI\Actions\HighLevel\HighLevelController;
use BitCode\FI\Core\Util\Route;

Route::post('highLevel_authorization', [HighLevelController::class, 'highLevelAuthorization']);
Route::post('get_highLevel_contact_custom_fields', [HighLevelController::class, 'getCustomFields']);
Route::post('highLevel_fetch_all_tags', [HighLevelController::class, 'getAllTags']);
