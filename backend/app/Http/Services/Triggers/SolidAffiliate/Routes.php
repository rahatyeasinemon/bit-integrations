<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;
use BitApps\BTCBI\Http\Services\Triggers\SolidAffiliate\SolidAffiliateController;

Route::get('solidaffiliate/get', [SolidAffiliateController::class, 'getAll']);
Route::post('solidaffiliate/get/form', [SolidAffiliateController::class, 'get_a_form']);
