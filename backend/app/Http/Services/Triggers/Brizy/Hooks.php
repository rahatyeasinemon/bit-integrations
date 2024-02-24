<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\Brizy\BrizyController;

Hooks::addFilter('brizy_form_submit_data', [BrizyController::class, 'handle_brizy_submit'], 10, 2);
