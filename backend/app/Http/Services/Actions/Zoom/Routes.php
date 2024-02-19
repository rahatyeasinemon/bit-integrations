<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\Zoom\ZoomController;
use BitCode\BTCBI\Util\Route;

Route::post('zoom_generate_token', [ZoomController::class, 'authorization']);
Route::post('zoom_fetch_all_meetings', [ZoomController::class, 'zoomFetchAllMeetings']);
