<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\Kadence\KadenceController;

Hooks::addAction('kadence_blocks_form_submission', [KadenceController::class, 'handle_kadence_form_submit'], 10, 4);
Hooks::addAction('kadence_blocks_advanced_form_submission', [KadenceController::class, 'handle_kadence_form_submit'], 10, 4);
