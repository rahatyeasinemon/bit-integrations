<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Zoom\ZoomController;
use BitApps\BTCBI\Util\Route;

Route::post('zoom_generate_token', [ZoomController::class, 'authorization']);
Route::post('zoom_fetch_all_meetings', [ZoomController::class, 'zoomFetchAllMeetings']);
