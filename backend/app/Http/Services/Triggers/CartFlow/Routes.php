<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Triggers\CartFlow\CartFlowController;

Route::get('cartflow/get', [CartFlowController::class, 'getAllForms']);
Route::post('cartflow/get/form', [CartFlowController::class, 'getFormFields']);
