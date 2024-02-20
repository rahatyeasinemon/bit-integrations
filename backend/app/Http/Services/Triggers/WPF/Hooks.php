<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Hooks;
use BitApps\BTCBI\Util\Request;
use BitApps\BTCBI\Http\Services\Triggers\WPF\WPFController;

if (Request::Check('frontend')) {
    Hooks::add('wpforms_process_complete', [WPFController::class, 'wpforms_process_complete'], 9999, 4);
}
