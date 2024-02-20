<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\SendFox\SendFoxController;
use BitApps\BTCBI\Util\Route;

Route::post('sendFox_authorize', [SendFoxController::class, 'sendFoxAuthorize']);
Route::post('sendfox_fetch_all_list', [SendFoxController::class, 'fetchContactLists']);
