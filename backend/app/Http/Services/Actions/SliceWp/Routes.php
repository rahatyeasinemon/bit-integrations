<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\SliceWp\SliceWpController;
use BitApps\BTCBI\Util\Route;

Route::post('slicewp_authorize', [SliceWpController::class, 'authorizeSliceWp']);
