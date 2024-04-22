<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\Selzy\SelzyController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('selzy_handle_authorize', [SelzyController::class, 'handleAuthorize']);
Route::post('selzy_get_all_tags', [SelzyController::class, 'getAllTags']);
Route::post('selzy_get_all_custom_fields', [SelzyController::class, 'getAllCustomFields']);
