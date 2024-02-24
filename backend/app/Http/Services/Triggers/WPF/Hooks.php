<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;

use BitApps\BTCBI\Http\Services\Triggers\WPF\WPFController;
use BTCBI\Deps\BitApps\WPKit\Http\RequestType;

if (RequestType::is('frontend')) {
    Hooks::addAction('wpforms_process_complete', [WPFController::class, 'wpforms_process_complete'], 9999, 4);
}
