<?php


if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Route;
use BitApps\BTCBI\Http\Services\Triggers\Bricks\BricksController;

Route::get('bricks/get', [BricksController::class, 'getAllForms']);
Route::post('bricks/get/form', [BricksController::class, 'getFormFields']);
