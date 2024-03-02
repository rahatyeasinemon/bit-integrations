<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Moosend\MoosendController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('moosend_handle_authorize', [MoosendController::class, 'handleAuthorize']);
