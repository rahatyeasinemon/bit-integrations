<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Hooks;
use BitCode\BTCBI\Triggers\GF\GFController;

Hooks::add('gform_after_submission', [GFController::class, 'gform_after_submission'], 10, 2);
