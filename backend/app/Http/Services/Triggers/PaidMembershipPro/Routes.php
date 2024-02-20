<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Route;
use BitApps\BTCBI\Http\Services\Triggers\PaidMembershipPro\PaidMembershipProController;

Route::get('paidmembershippro/get', [PaidMembershipProController::class, 'getAll']);
Route::post('paidmembershippro/get/form', [PaidMembershipProController::class, 'get_a_form']);

Route::get('get_all_paid_membership_pro_level', [PaidMembershipProController::class, 'getAllPaidMembershipProLevel']);
