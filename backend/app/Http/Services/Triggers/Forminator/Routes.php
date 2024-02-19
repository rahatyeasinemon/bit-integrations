<?php

if (!defined('ABSPATH')) {
    exit;
}
use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Http\Services\Triggers\Forminator\ForminatorController;

Route::get('forminator/get', [ForminatorController::class, 'getAll']);
Route::post('forminator/get/form', [ForminatorController::class, 'get_a_form']);
