<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\Zoom\ZoomController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('zoom_generate_token', [ZoomController::class, 'authorization']);
Route::post('zoom_fetch_all_meetings', [ZoomController::class, 'zoomFetchAllMeetings']);
