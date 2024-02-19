<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Hooks;
use BitCode\BTCBI\Http\Services\Triggers\PiotnetForms\PiotnetFormsController;

Hooks::add('piotnetforms/form_builder/new_record', [PiotnetFormsController::class, 'handle_piotnet_submit']);
// Hooks::add('piotnetforms/form_builder/new_record_v2', [PiotnetFormsController::class, 'pro_handle_piotnet_submit']);
