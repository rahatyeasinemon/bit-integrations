<?php

if (!defined('ABSPATH')) {
    exit;
}


use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\EVF\EVFController;

// Hooks::addAction('ipt_fsqm_hook_save_insert', [EVFController::class, 'handleSubmission'], 10, 1);
Hooks::addAction('everest_forms_complete_entry_save', [EVFController::class, 'handleSubmission'], 10, 5);
