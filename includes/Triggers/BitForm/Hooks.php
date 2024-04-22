<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Core\Util\Hooks;
use BitApps\BTCBI_PRO\Triggers\BitForm\BitFormController;

Hooks::add('bitform_submit_success', [BitFormController::class, 'handle_bitform_submit'], 10, 3);
