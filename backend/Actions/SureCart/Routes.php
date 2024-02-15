<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\SureCart\SureCartController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('sureCart_authorization', [SureCartController::class, 'checkAuthorization']);
