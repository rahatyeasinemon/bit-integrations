<?php

if (!defined('ABSPATH')) {
    exit;
}


use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\WPEF\WPEFController;

// Hooks::addAction('ipt_fsqm_hook_save_insert', [WPEFController::class, 'handleSubmission'], 10, 1);
Hooks::addAction('ipt_fsqm_hook_save_success', [WPEFController::class, 'handleSubmission'], 10, 1);
