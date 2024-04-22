<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\GoogleContacts\GoogleContactsController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('googleContacts_authorization', [GoogleContactsController::class, 'authorization']);
