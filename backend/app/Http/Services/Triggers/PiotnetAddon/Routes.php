<?php


if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Route;
use BitApps\BTCBI\Http\Services\Triggers\PiotnetAddon\PiotnetAddonController;

Route::get('piotnetaddon/get', [PiotnetAddonController::class, 'getAllForms']);
Route::post('piotnetaddon/get/form', [PiotnetAddonController::class, 'getFormFields']);
