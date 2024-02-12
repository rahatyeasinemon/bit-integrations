<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\KirimEmail\KirimEmailController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('kirimEmail_authorization', [KirimEmailController::class, 'checkAuthorization']);
Route::post('kirimEmail_fetch_all_list', [KirimEmailController::class, 'getAllList']);
