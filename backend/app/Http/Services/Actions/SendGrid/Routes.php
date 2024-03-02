<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\SendGrid\SendGridController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('sendGrid_authentication', [SendGridController::class, 'authentication']);
Route::post('sendGrid_fetch_all_lists', [SendGridController::class, 'getLists']);
