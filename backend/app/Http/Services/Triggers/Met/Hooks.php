<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\Met\MetController;

Hooks::addAction('metform_pro_form_data_for_pro_integrations', [MetController::class, 'handle_metform_pro_submit'], 10, 3);
Hooks::addAction('metform_after_store_form_data', [MetController::class, 'handle_metform_submit'], 10, 3);
