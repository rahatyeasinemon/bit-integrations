<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\Getgist\GetgistController;
use BitCode\BTCBI\Util\Route;

Route::post('getgist_authorize', [GetgistController::class, 'getgistAuthorize']);
