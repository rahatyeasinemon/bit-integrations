<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Mailify\MailifyController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('mailify_authorize', [MailifyController::class, 'authorization']);
Route::post('mailify_lists', [MailifyController::class, 'getAllList']);
Route::post('mailify_headers', [MailifyController::class, 'mailifyHeaders']);
