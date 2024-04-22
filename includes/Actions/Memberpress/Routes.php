<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\Memberpress\MemberpressController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('memberpress_authorize', [MemberpressController::class, 'authorizeMemberpress']);
Route::post('fetch_all_membership', [MemberpressController::class, 'getAllMembership']);
Route::post('fetch_all_payment_gateway', [MemberpressController::class, 'allPaymentGateway']);
