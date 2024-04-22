<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\KirimEmail\KirimEmailController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('kirimEmail_authorization', [KirimEmailController::class, 'checkAuthorization']);
Route::post('kirimEmail_fetch_all_list', [KirimEmailController::class, 'getAllList']);
