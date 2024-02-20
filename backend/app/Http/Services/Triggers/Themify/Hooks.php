<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\Themify\ThemifyController;

// Hooks::add('wp_ajax_tb_signup_process', [ThemifyController::class, 'handle_themify_submit']);

Hooks::add('themify_builder_after_template_content_render', [ThemifyController::class, 'handle_themify_submit']);
