<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\GF\GFController;

Hooks::addAction('gform_after_submission', [GFController::class, 'gform_after_submission'], 10, 2);
