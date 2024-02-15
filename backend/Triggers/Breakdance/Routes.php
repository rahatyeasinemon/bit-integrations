<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Core\Util\Route;
use BitCode\BTCBI\Triggers\Breakdance\BreakdanceController;

Route::get('breakdance/get', [BreakdanceController::class, 'getAllForms']);
Route::post('breakdance/get/form', [BreakdanceController::class, 'getFormFields']);

BreakdanceController::addAction();
