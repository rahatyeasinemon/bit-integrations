<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\FI\Core\Util\Hooks;
use BitCode\FI\Triggers\FormHook\FormHookController;

Hooks::add('uagb_form_success', [FormHookController::class, 'formHookHandler'], 10, PHP_INT_MAX);
