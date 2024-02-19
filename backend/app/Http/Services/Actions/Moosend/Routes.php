<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\Moosend\MoosendController;
use BitCode\BTCBI\Util\Route;

Route::post('moosend_handle_authorize', [MoosendController::class, 'handleAuthorize']);
