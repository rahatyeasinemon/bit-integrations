<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;
use BitApps\BTCBI\Http\Services\Triggers\Breakdance\BreakdanceController;

Route::get('breakdance/get', [BreakdanceController::class, 'getAllForms']);
Route::post('breakdance/get/form', [BreakdanceController::class, 'getFormFields']);

BreakdanceController::addAction();
