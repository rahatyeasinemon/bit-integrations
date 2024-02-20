<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Freshdesk\FreshdeskController;
use BitApps\BTCBI\Util\Route;

Route::post('freshdesk_authorization_and_fetch_tickets', [FreshdeskController::class, 'checkAuthorizationAndFetchTickets']);
