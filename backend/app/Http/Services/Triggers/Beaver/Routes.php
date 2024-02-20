<?php


if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Route;
use BitApps\BTCBI\Http\Services\Triggers\Beaver\BeaverController;

Route::get('beaver/get', [BeaverController::class, 'getAllForms']);
Route::post('beaver/get/form', [BeaverController::class, 'getFormFields']);
