<?php
if (!defined('ABSPATH')) {
    exit;
}


use BitCode\BTCBI\Core\Util\Hooks;
use BitCode\BTCBI\Triggers\WPEF\WPEFController;

// Hooks::add('ipt_fsqm_hook_save_insert', [WPEFController::class, 'handleSubmission'], 10, 1);
Hooks::add('ipt_fsqm_hook_save_success', [WPEFController::class, 'handleSubmission'], 10, 1);
