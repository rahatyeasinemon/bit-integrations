<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Core\Util\Hooks;
use BitCode\BTCBI\Triggers\FormCraft\FormCraftController;

Hooks::add('formcraft_after_save', [FormCraftController::class, 'handle_formcraft_submit'], 10, 4);
