<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Hooks;
use BitCode\BTCBI\Http\Services\Triggers\Tripetto\TripettoController;

Hooks::add('tripetto_submit', [TripettoController::class, 'handleTripettoSubmit'], 10, 2);
