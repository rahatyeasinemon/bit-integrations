<?php

if (!defined('ABSPATH')) {
    exit;
}
use BitCode\BTCBI\Core\Util\Route;
use BitCode\BTCBI\Triggers\FormCraft\FormCraftController;

Route::get('formcraft/get', [FormCraftController::class, 'getAll']);
Route::post('formcraft/get/form', [FormCraftController::class, 'get_a_form']);
