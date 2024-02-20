<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Autonami\AutonamiController;
use BitApps\BTCBI\Util\Route;

Route::post('autonami_authorize', [AutonamiController::class, 'autonamiAuthorize']);
Route::post('autonami_lists_and_tags', [AutonamiController::class, 'autonamiListsAndTags']);
Route::post('autonami_fields', [AutonamiController::class, 'autonamiFields']);
