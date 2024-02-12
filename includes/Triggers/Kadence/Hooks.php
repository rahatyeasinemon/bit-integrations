<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Core\Util\Hooks;
use BitCode\BTCBI\Triggers\Kadence\KadenceController;

Hooks::add('kadence_blocks_form_submission', [KadenceController::class, 'handle_kadence_form_submit'], 10, 4);
Hooks::add('kadence_blocks_advanced_form_submission', [KadenceController::class, 'handle_kadence_form_submit'], 10, 4);
