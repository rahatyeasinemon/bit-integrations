<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\PiotnetAddon\PiotnetAddonController;

// for piotnet addon field
// Hooks::addAction('pafe/form_builder/new_record', [PiotnetAddonController::class, 'handle_piotnet_submit']);
Hooks::addAction('pafe/form_builder/new_record_v2', [PiotnetAddonController::class, 'handle_piotnet_submit']);
