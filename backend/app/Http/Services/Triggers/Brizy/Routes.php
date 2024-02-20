<?php


if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Route;
use BitApps\BTCBI\Http\Services\Triggers\Brizy\BrizyController;

Route::get('brizy/get', [BrizyController::class, 'getAllForms']);
Route::post('brizy/get/form', [BrizyController::class, 'getFormFields']);
