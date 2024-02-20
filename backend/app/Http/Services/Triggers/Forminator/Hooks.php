<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\Forminator\ForminatorController;

Hooks::add('forminator_custom_form_submit_before_set_fields', [ForminatorController::class, 'handle_forminator_submit'], 10, 3);
