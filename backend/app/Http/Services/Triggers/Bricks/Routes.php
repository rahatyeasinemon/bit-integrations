<?php


if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;
use BitApps\BTCBI\Http\Services\Triggers\Bricks\BricksController;

Route::get('bricks/get', [BricksController::class, 'getAllForms']);
Route::post('bricks/get/form', [BricksController::class, 'getFormFields']);
