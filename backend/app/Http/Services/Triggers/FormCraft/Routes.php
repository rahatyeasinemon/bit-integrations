<?php

if (!defined('ABSPATH')) {
    exit;
}
use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Http\Services\Triggers\FormCraft\FormCraftController;

Route::get('formcraft/get', [FormCraftController::class, 'getAll']);
Route::post('formcraft/get/form', [FormCraftController::class, 'get_a_form']);
