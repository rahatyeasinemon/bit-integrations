<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\PaidMembershipPro\PaidMembershipProController;
use BitCode\BTCBI\Util\Route;

Route::post('paid_membership_pro_authorize', [PaidMembershipProController::class, 'authorizeMemberpress']);
Route::post('fetch_all_paid_membership_pro_level', [PaidMembershipProController::class, 'getAllPaidMembershipProLevel']);
