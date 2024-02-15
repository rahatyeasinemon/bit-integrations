<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\SendGrid\SendGridController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('sendGrid_authentication', [SendGridController::class, 'authentication']);
Route::post('sendGrid_fetch_all_lists', [SendGridController::class, 'getLists']);
