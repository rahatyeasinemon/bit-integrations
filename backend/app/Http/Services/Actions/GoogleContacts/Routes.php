<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\GoogleContacts\GoogleContactsController;
use BitApps\BTCBI\Util\Route;

Route::post('googleContacts_authorization', [GoogleContactsController::class, 'authorization']);
