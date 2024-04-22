<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\SureCart\SureCartController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('sureCart_authorization', [SureCartController::class, 'checkAuthorization']);
