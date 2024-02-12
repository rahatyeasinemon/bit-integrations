<?php
if (!defined('ABSPATH')) {
    exit;
}
use BitCode\BTCBI\Core\Util\Route;
use BitCode\BTCBI\Triggers\WSForm\WSFormController;

Route::get('wsform/get', [WSFormController::class, 'getAll']);
Route::post('wsform/get/form', [WSFormController::class, 'get_a_form']);