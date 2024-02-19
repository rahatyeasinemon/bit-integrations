<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\SureCart\SureCartController;
use BitCode\BTCBI\Util\Route;

Route::post('sureCart_authorization', [SureCartController::class, 'checkAuthorization']);
