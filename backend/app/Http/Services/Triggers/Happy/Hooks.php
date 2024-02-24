<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\Happy\HappyController;

Hooks::addAction('happyforms_submission_success', [HappyController::class, 'handle_happy_submit'], 10, 3);
