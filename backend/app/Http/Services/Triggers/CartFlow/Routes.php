<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;
use BitApps\BTCBI\Http\Services\Triggers\CartFlow\CartFlowController;

Route::get('cartflow/get', [CartFlowController::class, 'getAllForms']);
Route::post('cartflow/get/form', [CartFlowController::class, 'getFormFields']);
