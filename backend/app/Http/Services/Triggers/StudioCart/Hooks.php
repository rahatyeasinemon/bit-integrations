<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\StudioCart\StudioCartController;

Hooks::addAction('sc_order_complete', [StudioCartController::class, 'newOrderCreated'], 10, 3);
