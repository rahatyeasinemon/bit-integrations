<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\WSForm\WSFormController;

Hooks::add('ws_form_action_for_bi', [WSFormController::class, 'handle_ws_form_submit'], 9999, 4);
