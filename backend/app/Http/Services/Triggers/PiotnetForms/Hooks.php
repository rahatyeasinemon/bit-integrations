<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\PiotnetForms\PiotnetFormsController;

Hooks::addAction('piotnetforms/form_builder/new_record', [PiotnetFormsController::class, 'handle_piotnet_submit']);
// Hooks::addAction('piotnetforms/form_builder/new_record_v2', [PiotnetFormsController::class, 'pro_handle_piotnet_submit']);
