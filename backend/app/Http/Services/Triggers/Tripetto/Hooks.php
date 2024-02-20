<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\Tripetto\TripettoController;

Hooks::add('tripetto_submit', [TripettoController::class, 'handleTripettoSubmit'], 10, 2);
