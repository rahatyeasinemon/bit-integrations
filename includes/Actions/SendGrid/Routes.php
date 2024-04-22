<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\SendGrid\SendGridController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('sendGrid_authentication', [SendGridController::class, 'authentication']);
Route::post('sendGrid_fetch_all_lists', [SendGridController::class, 'getLists']);
