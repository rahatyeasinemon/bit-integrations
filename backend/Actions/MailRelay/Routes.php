<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\MailRelay\MailRelayController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('mailRelay_authentication', [MailRelayController::class, 'authentication']);
Route::post('mailRelay_fetch_all_groups', [MailRelayController::class, 'getAllGroups']);
