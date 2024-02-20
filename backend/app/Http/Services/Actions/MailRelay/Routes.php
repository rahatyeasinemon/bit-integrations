<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\MailRelay\MailRelayController;
use BitApps\BTCBI\Util\Route;

Route::post('mailRelay_authentication', [MailRelayController::class, 'authentication']);
Route::post('mailRelay_fetch_all_groups', [MailRelayController::class, 'getAllGroups']);
