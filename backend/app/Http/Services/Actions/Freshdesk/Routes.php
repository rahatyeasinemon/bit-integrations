<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\Freshdesk\FreshdeskController;
use BitCode\BTCBI\Util\Route;

Route::post('freshdesk_authorization_and_fetch_tickets', [FreshdeskController::class, 'checkAuthorizationAndFetchTickets']);
