<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Hooks;
use BitCode\BTCBI\Triggers\Happy\HappyController;

Hooks::add('happyforms_submission_success', [HappyController::class, 'handle_happy_submit'], 10, 3);
