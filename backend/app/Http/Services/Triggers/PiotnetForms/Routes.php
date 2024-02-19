<?php

if (!defined('ABSPATH')) {
    exit;
}
use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Triggers\PiotnetForms\PiotnetFormsController;

Route::get('piotnetforms/get', [PiotnetFormsController::class, 'getAll']);
Route::post('piotnetforms/get/form', [PiotnetFormsController::class, 'get_a_form']);
