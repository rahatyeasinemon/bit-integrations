<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\FormCraft\FormCraftController;

Hooks::add('formcraft_after_save', [FormCraftController::class, 'handle_formcraft_submit'], 10, 4);
