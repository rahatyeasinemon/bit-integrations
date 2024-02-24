<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\FormCraft\FormCraftController;

Hooks::addAction('formcraft_after_save', [FormCraftController::class, 'handle_formcraft_submit'], 10, 4);
