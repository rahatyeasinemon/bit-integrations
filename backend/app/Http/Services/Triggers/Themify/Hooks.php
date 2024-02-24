<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\Themify\ThemifyController;

// Hooks::addAction('wp_ajax_tb_signup_process', [ThemifyController::class, 'handle_themify_submit']);

Hooks::addAction('themify_builder_after_template_content_render', [ThemifyController::class, 'handle_themify_submit']);
