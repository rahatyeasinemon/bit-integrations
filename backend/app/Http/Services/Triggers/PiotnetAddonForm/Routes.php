<?php


if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;
use BitApps\BTCBI\Http\Services\Triggers\PiotnetAddonForm\PiotnetAddonFormController;

Route::get('piotnetaddonform/get', [PiotnetAddonFormController::class, 'getAllForms']);
Route::post('piotnetaddonform/get/form', [PiotnetAddonFormController::class, 'getFormFields']);
