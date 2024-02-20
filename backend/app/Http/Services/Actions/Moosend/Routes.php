<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Moosend\MoosendController;
use BitApps\BTCBI\Util\Route;

Route::post('moosend_handle_authorize', [MoosendController::class, 'handleAuthorize']);
