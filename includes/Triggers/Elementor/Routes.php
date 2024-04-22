<?php


if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Core\Util\Route;
use BitApps\BTCBI_PRO\Triggers\Elementor\ElementorController;

Route::post('elementor/test', [ElementorController::class, 'getTestData']);
Route::post('elementor/test/remove', [ElementorController::class, 'removeTestData']);
