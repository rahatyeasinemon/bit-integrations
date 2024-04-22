<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\MailRelay\MailRelayController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('mailRelay_authentication', [MailRelayController::class, 'authentication']);
Route::post('mailRelay_fetch_all_groups', [MailRelayController::class, 'getAllGroups']);
