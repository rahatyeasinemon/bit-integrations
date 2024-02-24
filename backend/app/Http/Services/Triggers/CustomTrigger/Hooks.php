<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Triggers\CustomTrigger\CustomTriggerController;
use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;

Hooks::addAction('bit_integrations_custom_trigger', [CustomTriggerController::class, 'handleCustomTrigger'], 10, 2);
