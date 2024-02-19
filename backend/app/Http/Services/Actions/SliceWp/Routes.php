<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\SliceWp\SliceWpController;
use BitCode\BTCBI\Util\Route;

Route::post('slicewp_authorize', [SliceWpController::class, 'authorizeSliceWp']);
