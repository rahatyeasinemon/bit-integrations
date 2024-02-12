<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Core\Util\Hooks;
use BitCode\BTCBI\Triggers\ARForm\ARFormController;

Hooks::add('arfliteentryexecute', [ARFormController::class, 'handleArFormSubmit'], 10, 4);
Hooks::add('arfentryexecute', [ARFormController::class, 'handleArFormSubmit'], 10, 4);
