<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Triggers\CustomTrigger\CustomTriggerController;
use BitCode\BTCBI\Core\Util\Hooks;

Hooks::add('bit_integrations_custom_trigger', [CustomTriggerController::class, 'handleCustomTrigger'], 10, 2);
