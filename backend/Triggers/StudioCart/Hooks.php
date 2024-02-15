<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Core\Util\Hooks;
use BitCode\BTCBI\Triggers\StudioCart\StudioCartController;

Hooks::add('sc_order_complete', [StudioCartController::class, 'newOrderCreated'], 10, 3);
