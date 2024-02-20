<?php

if (!defined('ABSPATH')) {
    exit;
}
use BitApps\BTCBI\Util\Route;
use BitApps\BTCBI\Http\Services\Triggers\Forminator\ForminatorController;

Route::get('forminator/get', [ForminatorController::class, 'getAll']);
Route::post('forminator/get/form', [ForminatorController::class, 'get_a_form']);
