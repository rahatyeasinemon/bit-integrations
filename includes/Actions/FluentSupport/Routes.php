<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\FluentSupport\FluentSupportController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('fluentSupport_authorization', [FluentSupportController::class, 'checkAuthorization']);
Route::post('fluent_support_get_all_support_staff', [FluentSupportController::class, 'getAllSupportStaff']);
Route::post('fluent_support_get_all_business_inboxes', [FluentSupportController::class, 'getAllBusinessInboxes']);
