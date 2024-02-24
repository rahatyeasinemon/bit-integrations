<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\Divi\DiviController;

Hooks::addAction('et_pb_contact_form_submit', [DiviController::class, 'handle_divi_submit'], 10, 3);
