<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\Klaviyo\KlaviyoController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('klaviyo_handle_authorize', [klaviyoController::class, 'handleAuthorize']);
