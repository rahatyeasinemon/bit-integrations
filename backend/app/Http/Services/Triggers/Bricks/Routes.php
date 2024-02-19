<?php


if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Triggers\Bricks\BricksController;

Route::get('bricks/get', [BricksController::class, 'getAllForms']);
Route::post('bricks/get/form', [BricksController::class, 'getFormFields']);
