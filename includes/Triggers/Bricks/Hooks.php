<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Core\Util\Hooks;
use BitCode\BTCBI\Triggers\Bricks\BricksController;

Hooks::add('bricks/form/custom_action', [BricksController::class, 'handle_bricks_submit']);
