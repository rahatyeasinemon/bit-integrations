<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Core\Util\Hooks;
use BitCode\BTCBI\Triggers\Formidable\FormidableController;

Hooks::add('frm_success_action', [FormidableController::class, 'handle_formidable_submit'], 10, 5);
