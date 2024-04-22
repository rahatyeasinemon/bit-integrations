<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\Moosend\MoosendController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('moosend_handle_authorize', [MoosendController::class, 'handleAuthorize']);
