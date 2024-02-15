<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\ZoomWebinar\ZoomWebinarController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('zoom_webinar_generate_token', [ZoomWebinarController::class, 'authorization']);
Route::post('zoom_webinar_fetch_all_webinar', [ZoomWebinarController::class, 'zoomFetchAllWebinar']);
