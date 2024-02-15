<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Core\Util\Hooks;
use BitCode\BTCBI\Triggers\Tripetto\TripettoController;

Hooks::add('tripetto_submit', [TripettoController::class, 'handleTripettoSubmit'], 10, 2);
