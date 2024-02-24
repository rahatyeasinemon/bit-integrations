<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\WeForms\WeFormsController;

Hooks::addAction('weforms_entry_submission', [WeFormsController::class, 'handle_weforms_submit'], 10, 4);
