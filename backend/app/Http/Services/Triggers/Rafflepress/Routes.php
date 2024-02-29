<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;
use BitApps\BTCBI\Http\Services\Triggers\Rafflepress\RafflepressController;

Route::get('rafflepress/get', [RafflepressController::class, 'getAll']);
Route::post('rafflepress/get/form', [RafflepressController::class, 'get_a_form']);
