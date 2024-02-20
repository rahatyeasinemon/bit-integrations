<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\Bricks\BricksController;

Hooks::add('bricks/form/custom_action', [BricksController::class, 'handle_bricks_submit']);
