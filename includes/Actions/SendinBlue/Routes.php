<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\SendinBlue\SendinBlueController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('sblue_authorize', [SendinBlueController::class, 'sendinBlueAuthorize']);
Route::post('sblue_refresh_lists', [SendinBlueController::class, 'refreshlists']);
Route::post('sblue_headers', [SendinBlueController::class, 'sendinblueHeaders']);
Route::post('sblue_refresh_template', [SendinBlueController::class, 'refreshTemplate']);
