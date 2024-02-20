<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\StudioCart\StudioCartController;

Hooks::add('sc_order_complete', [StudioCartController::class, 'newOrderCreated'], 10, 3);
