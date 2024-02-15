<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\SliceWp\SliceWpController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('slicewp_authorize', [SliceWpController::class, 'authorizeSliceWp']);
