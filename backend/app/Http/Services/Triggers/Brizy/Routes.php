<?php


if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Http\Services\Triggers\Brizy\BrizyController;

Route::get('brizy/get', [BrizyController::class, 'getAllForms']);
Route::post('brizy/get/form', [BrizyController::class, 'getFormFields']);
