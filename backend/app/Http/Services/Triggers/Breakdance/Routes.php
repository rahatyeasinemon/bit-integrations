<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Http\Services\Triggers\Breakdance\BreakdanceController;

Route::get('breakdance/get', [BreakdanceController::class, 'getAllForms']);
Route::post('breakdance/get/form', [BreakdanceController::class, 'getFormFields']);

BreakdanceController::addAction();
