<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\Getgist\GetgistController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('getgist_authorize', [GetgistController::class, 'getgistAuthorize']);
