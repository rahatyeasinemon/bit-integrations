<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\Elementor\ElementorController;

Hooks::addAction('elementor_pro/forms/new_record', [ElementorController::class, 'handle_elementor_submit']);
