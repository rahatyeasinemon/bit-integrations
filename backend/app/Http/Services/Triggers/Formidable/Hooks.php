<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\Formidable\FormidableController;

Hooks::add('frm_success_action', [FormidableController::class, 'handle_formidable_submit'], 10, 5);
