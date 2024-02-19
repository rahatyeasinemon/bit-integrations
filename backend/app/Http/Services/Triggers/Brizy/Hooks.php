<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Hooks;
use BitCode\BTCBI\Triggers\Brizy\BrizyController;

Hooks::filter('brizy_form_submit_data', [BrizyController::class, 'handle_brizy_submit'], 10, 2);
