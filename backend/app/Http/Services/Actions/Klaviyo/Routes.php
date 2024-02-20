<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Klaviyo\KlaviyoController;
use BitApps\BTCBI\Util\Route;

Route::post('klaviyo_handle_authorize', [klaviyoController::class, 'handleAuthorize']);
