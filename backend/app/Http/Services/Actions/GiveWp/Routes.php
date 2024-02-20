<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\GiveWp\GiveWpController;
use BitApps\BTCBI\Util\Route;

Route::post('giveWp_authorize', [GiveWpController::class, 'authorizeGiveWp']);
