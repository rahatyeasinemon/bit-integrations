<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\WeForms\WeFormsController;

Hooks::add('weforms_entry_submission', [WeFormsController::class, 'handle_weforms_submit'], 10, 4);
