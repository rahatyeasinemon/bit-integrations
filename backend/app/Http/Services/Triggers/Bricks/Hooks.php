<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\Bricks\BricksController;

Hooks::addAction('bricks/form/custom_action', [BricksController::class, 'handle_bricks_submit']);
