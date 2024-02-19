<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Http\Services\Triggers\Rafflepress\RafflepressController;

Route::get('rafflepress/get', [RafflepressController::class, 'getAll']);
Route::post('rafflepress/get/form', [RafflepressController::class, 'get_a_form']);
