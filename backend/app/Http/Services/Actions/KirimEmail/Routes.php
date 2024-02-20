<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\KirimEmail\KirimEmailController;
use BitApps\BTCBI\Util\Route;

Route::post('kirimEmail_authorization', [KirimEmailController::class, 'checkAuthorization']);
Route::post('kirimEmail_fetch_all_list', [KirimEmailController::class, 'getAllList']);
