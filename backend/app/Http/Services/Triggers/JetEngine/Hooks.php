<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\JetEngine\JetEngineController;

Hooks::addAction('updated_post_meta', [JetEngineController::class, 'post_meta_data'], 10, 4);
Hooks::addAction('updated_post_meta', [JetEngineController::class, 'post_meta_value_check'], 10, 4);
