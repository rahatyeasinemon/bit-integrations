<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\BitForm\BitFormController;

Hooks::addAction('bitform_submit_success', [BitFormController::class, 'handle_bitform_submit'], 10, 3);
