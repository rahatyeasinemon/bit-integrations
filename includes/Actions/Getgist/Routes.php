<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\Getgist\GetgistController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('getgist_authorize', [GetgistController::class, 'getgistAuthorize']);
