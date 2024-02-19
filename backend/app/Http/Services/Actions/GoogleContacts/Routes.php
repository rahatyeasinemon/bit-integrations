<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\GoogleContacts\GoogleContactsController;
use BitCode\BTCBI\Util\Route;

Route::post('googleContacts_authorization', [GoogleContactsController::class, 'authorization']);
