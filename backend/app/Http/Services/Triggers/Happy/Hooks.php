<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\Happy\HappyController;

Hooks::add('happyforms_submission_success', [HappyController::class, 'handle_happy_submit'], 10, 3);
