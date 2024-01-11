<?php


if (!defined('ABSPATH')) {
    exit;
}

use BitCode\FI\Core\Util\Route;
use BitCode\FI\Triggers\CaptureAction\CaptureActionController;

Route::post('capture_action/test', [CaptureActionController::class, 'getTestData']);
Route::post('capture_action/test/remove', [CaptureActionController::class, 'removeTestData']);
