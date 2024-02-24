<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\Tripetto\TripettoController;

Hooks::addAction('tripetto_submit', [TripettoController::class, 'handleTripettoSubmit'], 10, 2);
