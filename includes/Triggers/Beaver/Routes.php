<?php


if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Core\Util\Route;
use BitCode\BTCBI\Triggers\Beaver\BeaverController;

Route::get('beaver/get', [BeaverController::class, 'getAllForms']);
Route::post('beaver/get/form', [BeaverController::class, 'getFormFields']);