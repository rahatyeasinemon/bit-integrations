<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\Formidable\FormidableController;

Hooks::addAction('frm_success_action', [FormidableController::class, 'handle_formidable_submit'], 10, 5);
