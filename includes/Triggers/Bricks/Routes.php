<?php


if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Core\Util\Route;
use BitApps\BTCBI_PRO\Triggers\Bricks\BricksController;

Route::get('bricks/get', [BricksController::class, 'getAllForms']);
Route::post('bricks/get/form', [BricksController::class, 'getFormFields']);
