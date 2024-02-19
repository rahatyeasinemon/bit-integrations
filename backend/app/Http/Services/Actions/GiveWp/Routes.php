<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\GiveWp\GiveWpController;
use BitCode\BTCBI\Util\Route;

Route::post('giveWp_authorize', [GiveWpController::class, 'authorizeGiveWp']);
