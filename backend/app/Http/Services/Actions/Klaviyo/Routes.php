<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Klaviyo\KlaviyoController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('klaviyo_handle_authorize', [klaviyoController::class, 'handleAuthorize']);
