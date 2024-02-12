<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\SendFox\SendFoxController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('sendFox_authorize', [SendFoxController::class, 'sendFoxAuthorize']);
Route::post('sendfox_fetch_all_list', [SendFoxController::class, 'fetchContactLists']);
