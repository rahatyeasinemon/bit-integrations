<?php


if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Http\Services\Triggers\PiotnetAddonForm\PiotnetAddonFormController;

Route::get('piotnetaddonform/get', [PiotnetAddonFormController::class, 'getAllForms']);
Route::post('piotnetaddonform/get/form', [PiotnetAddonFormController::class, 'getFormFields']);
