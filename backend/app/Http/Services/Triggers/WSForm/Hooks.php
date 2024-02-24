<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\WSForm\WSFormController;

Hooks::addAction('ws_form_action_for_bi', [WSFormController::class, 'handle_ws_form_submit'], 9999, 4);
