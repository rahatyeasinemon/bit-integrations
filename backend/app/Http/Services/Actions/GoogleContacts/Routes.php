<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\GoogleContacts\GoogleContactsController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('googleContacts_authorization', [GoogleContactsController::class, 'authorization']);
