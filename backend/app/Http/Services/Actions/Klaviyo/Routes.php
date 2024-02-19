<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\Klaviyo\KlaviyoController;
use BitCode\BTCBI\Util\Route;

Route::post('klaviyo_handle_authorize', [klaviyoController::class, 'handleAuthorize']);
