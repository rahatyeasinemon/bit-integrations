<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\PaidMembershipPro\PaidMembershipProController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('paid_membership_pro_authorize', [PaidMembershipProController::class, 'authorizeMemberpress']);
Route::post('fetch_all_paid_membership_pro_level', [PaidMembershipProController::class, 'getAllPaidMembershipProLevel']);
