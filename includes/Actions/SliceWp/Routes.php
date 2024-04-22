<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\SliceWp\SliceWpController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('slicewp_authorize', [SliceWpController::class, 'authorizeSliceWp']);
