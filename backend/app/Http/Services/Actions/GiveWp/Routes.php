<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\GiveWp\GiveWpController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('giveWp_authorize', [GiveWpController::class, 'authorizeGiveWp']);
