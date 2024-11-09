<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\FI\Actions\Voxel\VoxelController;
use BitCode\FI\Core\Util\Route;

Route::post('voxel_authentication', [VoxelController::class, 'authentication']);
Route::post('get_the_events_calendar_events', [VoxelController::class, 'getAllEvents']);
