<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\Forminator\ForminatorController;

Hooks::addAction('forminator_custom_form_submit_before_set_fields', [ForminatorController::class, 'handle_forminator_submit'], 10, 3);
