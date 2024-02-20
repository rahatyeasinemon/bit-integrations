<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Getgist\GetgistController;
use BitApps\BTCBI\Util\Route;

Route::post('getgist_authorize', [GetgistController::class, 'getgistAuthorize']);
