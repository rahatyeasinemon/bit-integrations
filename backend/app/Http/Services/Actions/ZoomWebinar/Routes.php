<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\ZoomWebinar\ZoomWebinarController;
use BitApps\BTCBI\Util\Route;

Route::post('zoom_webinar_generate_token', [ZoomWebinarController::class, 'authorization']);
Route::post('zoom_webinar_fetch_all_webinar', [ZoomWebinarController::class, 'zoomFetchAllWebinar']);
