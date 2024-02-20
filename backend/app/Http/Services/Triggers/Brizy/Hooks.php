<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\Brizy\BrizyController;

Hooks::filter('brizy_form_submit_data', [BrizyController::class, 'handle_brizy_submit'], 10, 2);
