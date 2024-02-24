<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\ARForm\ARFormController;

Hooks::addAction('arfliteentryexecute', [ARFormController::class, 'handleArFormSubmit'], 10, 4);
Hooks::addAction('arfentryexecute', [ARFormController::class, 'handleArFormSubmit'], 10, 4);
