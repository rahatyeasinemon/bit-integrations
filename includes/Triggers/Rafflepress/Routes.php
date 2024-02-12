<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Core\Util\Route;
use BitCode\BTCBI\Triggers\Rafflepress\RafflepressController;

Route::get('rafflepress/get', [RafflepressController::class, 'getAll']);
Route::post('rafflepress/get/form', [RafflepressController::class, 'get_a_form']);
