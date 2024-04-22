<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Core\Util\Route;
use BitApps\BTCBI_PRO\Triggers\Breakdance\BreakdanceController;

Route::post('breakdance/test', [BreakdanceController::class, 'getTestData']);
Route::post('breakdance/test/remove', [BreakdanceController::class, 'removeTestData']);

BreakdanceController::addAction();
