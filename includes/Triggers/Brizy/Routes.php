<?php


if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Core\Util\Route;
use BitApps\BTCBI_PRO\Triggers\Brizy\BrizyController;

Route::get('brizy/get', [BrizyController::class, 'getAllForms']);
Route::post('brizy/get/form', [BrizyController::class, 'getFormFields']);
