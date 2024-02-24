<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\PiotnetAddonForm\PiotnetAddonFormController;

// for piotnet addon field
// Hooks::addAction('pafe/form_builder/new_record', [PiotnetAddonFormController::class, 'handle_piotnet_submit']);
Hooks::addAction('pafe/form_builder/new_record_v2', [PiotnetAddonFormController::class, 'handle_piotnet_submit']);
