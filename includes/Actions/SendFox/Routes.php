<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\SendFox\SendFoxController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('sendFox_authorize', [SendFoxController::class, 'sendFoxAuthorize']);
Route::post('sendfox_fetch_all_list', [SendFoxController::class, 'fetchContactLists']);
