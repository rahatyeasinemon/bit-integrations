<?php

if (!defined('ABSPATH')) {
    exit;
}


use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\NF\NFController;

Hooks::addAction('ninja_forms_after_submission', [NFController::class, 'ninja_forms_after_submission'], 10, 1);
