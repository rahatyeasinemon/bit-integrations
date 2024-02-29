<?php


if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;
use BitApps\BTCBI\Http\Services\Triggers\Beaver\BeaverController;

Route::get('beaver/get', [BeaverController::class, 'getAllForms']);
Route::post('beaver/get/form', [BeaverController::class, 'getFormFields']);
