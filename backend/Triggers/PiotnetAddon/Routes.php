<?php


if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Core\Util\Route;
use BitCode\BTCBI\Triggers\PiotnetAddon\PiotnetAddonController;

Route::get('piotnetaddon/get', [PiotnetAddonController::class, 'getAllForms']);
Route::post('piotnetaddon/get/form', [PiotnetAddonController::class, 'getFormFields']);
