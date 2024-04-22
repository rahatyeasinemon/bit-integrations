<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Triggers\CustomTrigger\CustomTriggerController;
use BitApps\BTCBI_PRO\Core\Util\Hooks;

Hooks::add('bit_integrations_custom_trigger', [CustomTriggerController::class, 'handleCustomTrigger'], 10, 2);
