<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\PaidMembershipPro\PaidMembershipProController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('paid_membership_pro_authorize', [PaidMembershipProController::class, 'authorizeMemberpress']);
Route::post('fetch_all_paid_membership_pro_level', [PaidMembershipProController::class, 'getAllPaidMembershipProLevel']);
