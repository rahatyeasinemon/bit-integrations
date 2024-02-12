<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Core\Util\Hooks;
use BitCode\BTCBI\Triggers\PiotnetAddonForm\PiotnetAddonFormController;

// for piotnet addon field
// Hooks::add('pafe/form_builder/new_record', [PiotnetAddonFormController::class, 'handle_piotnet_submit']);
Hooks::add('pafe/form_builder/new_record_v2', [PiotnetAddonFormController::class, 'handle_piotnet_submit']);
