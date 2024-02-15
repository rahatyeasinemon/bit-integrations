<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\GoogleContacts\GoogleContactsController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('googleContacts_authorization', [GoogleContactsController::class, 'authorization']);
