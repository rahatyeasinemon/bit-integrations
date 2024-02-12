<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Core\Util\Route;
use BitCode\BTCBI\Triggers\SolidAffiliate\SolidAffiliateController;

Route::get('solidaffiliate/get', [SolidAffiliateController::class, 'getAll']);
Route::post('solidaffiliate/get/form', [SolidAffiliateController::class, 'get_a_form']);
