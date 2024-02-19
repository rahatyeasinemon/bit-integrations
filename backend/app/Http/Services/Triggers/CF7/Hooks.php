<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Triggers\CF7\CF7Controller;
use BitCode\BTCBI\Util\Hooks;

// Hooks::add('wpcf7_submit', [CF7Controller::class, 'handle_wpcf7_submit'], 10, 2);
Hooks::add('wpcf7_before_send_mail', [CF7Controller::class, 'handle_wpcf7_submit'], 10);
