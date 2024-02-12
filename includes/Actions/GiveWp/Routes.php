<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\GiveWp\GiveWpController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('giveWp_authorize', [GiveWpController::class, 'authorizeGiveWp']);
