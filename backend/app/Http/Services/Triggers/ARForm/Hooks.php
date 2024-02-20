<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\ARForm\ARFormController;

Hooks::add('arfliteentryexecute', [ARFormController::class, 'handleArFormSubmit'], 10, 4);
Hooks::add('arfentryexecute', [ARFormController::class, 'handleArFormSubmit'], 10, 4);
