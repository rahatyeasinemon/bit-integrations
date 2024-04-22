<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Core\Util\Hooks;
use BitApps\BTCBI_PRO\Triggers\Elementor\ElementorController;

Hooks::add('elementor_pro/forms/new_record', [ElementorController::class, 'handle_elementor_submit']);
