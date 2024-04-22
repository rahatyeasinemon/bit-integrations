<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Core\Util\Route;
use BitApps\BTCBI_PRO\Actions\Freshdesk\FreshdeskController;

Route::post('freshdesk_authorization_and_fetch_tickets', [FreshdeskController::class, 'checkAuthorizationAndFetchTickets']);
Route::post('freshdesk_fetch_ticket_fields', [FreshdeskController::class, 'getAllTicketFields']);
Route::post('freshdesk_fetch_Contact_fields', [FreshdeskController::class, 'getAllContactFields']);
