<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Getgist\GetgistController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('getgist_authorize', [GetgistController::class, 'getgistAuthorize']);
