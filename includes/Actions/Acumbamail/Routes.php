<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\Acumbamail\AcumbamailController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('acumbamail_authorization_and_fetch_subscriber_list', [ AcumbamailController::class, 'acumbamailAuthAndFetchSubscriberList']);
Route::post('acumbamail_fetch_all_list',[AcumbamailController::class, 'fetchAllLists']);
Route::post('acumbamail_refresh_fields',[AcumbamailController::class, 'acumbamailRefreshFields']);
