<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\SureCart\SureCartController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('sureCart_authorization', [SureCartController::class, 'checkAuthorization']);
