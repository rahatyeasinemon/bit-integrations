<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\Beaver\BeaverController;

Hooks::addAction('fl_module_contact_form_after_send', [BeaverController::class, 'beaver_contact_form_submitted'], 10, 6);
Hooks::addAction('fl_builder_login_form_submission_complete', [BeaverController::class, 'beaver_login_form_submitted'], 10, 5);
Hooks::addAction('fl_builder_subscribe_form_submission_complete', [BeaverController::class, 'beaver_subscribe_form_submitted'], 10, 6);
