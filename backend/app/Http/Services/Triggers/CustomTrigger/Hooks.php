<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Triggers\CustomTrigger\CustomTriggerController;
use BitApps\BTCBI\Util\Hooks;

Hooks::add('bit_integrations_custom_trigger', [CustomTriggerController::class, 'handleCustomTrigger'], 10, 2);
