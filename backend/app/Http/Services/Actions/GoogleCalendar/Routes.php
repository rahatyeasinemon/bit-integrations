<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\GoogleCalendar\GoogleCalendarController;
use BitCode\BTCBI\Util\Route;

Route::post('googleCalendar_authorization', [GoogleCalendarController::class, 'authorization']);
Route::post('googleCalendar_get_all_lists', [GoogleCalendarController::class, 'getAllCalendarLists']);
