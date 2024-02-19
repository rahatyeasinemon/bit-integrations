<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Hooks;
use BitCode\BTCBI\Triggers\WeForms\WeFormsController;

Hooks::add('weforms_entry_submission', [WeFormsController::class, 'handle_weforms_submit'], 10, 4);
