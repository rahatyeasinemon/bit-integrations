<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\SendinBlue\SendinBlueController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('sblue_authorize', [SendinBlueController::class, 'sendinBlueAuthorize']);
Route::post('sblue_refresh_lists', [SendinBlueController::class, 'refreshlists']);
Route::post('sblue_headers', [SendinBlueController::class, 'sendinblueHeaders']);
Route::post('sblue_refresh_template', [SendinBlueController::class, 'refreshTemplate']);
