<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Triggers\CF7\CF7Controller;
use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;

// Hooks::addAction('wpcf7_submit', [CF7Controller::class, 'handle_wpcf7_submit'], 10, 2);
Hooks::addAction('wpcf7_before_send_mail', [CF7Controller::class, 'handle_wpcf7_submit'], 10);
