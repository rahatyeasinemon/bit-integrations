<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Hooks;
use BitCode\BTCBI\Triggers\FF\FFController;

Hooks::add('fluentform_submission_inserted', [FFController::class, 'handle_ff_submit'], 10, 3);
