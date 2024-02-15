<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Core\Util\Hooks;
use BitCode\BTCBI\Triggers\Elementor\ElementorController;

Hooks::add('elementor_pro/forms/new_record', [ElementorController::class, 'handle_elementor_submit']);
