<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\SliceWp\SliceWpController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('slicewp_authorize', [SliceWpController::class, 'authorizeSliceWp']);
