<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\SureCart\SureCartController;
use BitApps\BTCBI\Util\Route;

Route::post('sureCart_authorization', [SureCartController::class, 'checkAuthorization']);
