<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\FF\FFController;

Hooks::addAction('fluentform_submission_inserted', [FFController::class, 'handle_ff_submit'], 10, 3);
