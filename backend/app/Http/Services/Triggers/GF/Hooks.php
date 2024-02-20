<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\GF\GFController;

Hooks::add('gform_after_submission', [GFController::class, 'gform_after_submission'], 10, 2);
