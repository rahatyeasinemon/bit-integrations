<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Mailercloud\MailercloudController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('mailercloud_handle_authorize', [MailercloudController::class, 'handleAuthorize']);
Route::post('mailercloud_get_all_lists', [MailercloudController::class, 'getAllLists']);
Route::post('mailercloud_get_all_fields', [MailercloudController::class, 'getAllFields']);
