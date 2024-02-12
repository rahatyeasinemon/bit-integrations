<?php
if (!defined('ABSPATH')) {
    exit;
}
use BitCode\BTCBI\Core\Util\Route;
use BitCode\BTCBI\Triggers\Forminator\ForminatorController;

Route::get('forminator/get', [ForminatorController::class, 'getAll']);
Route::post('forminator/get/form', [ForminatorController::class, 'get_a_form']);