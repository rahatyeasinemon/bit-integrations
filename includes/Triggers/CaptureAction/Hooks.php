<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\FI\Core\Util\Hooks;
use BitCode\FI\Triggers\CaptureAction\CaptureActionController;

Hooks::add('forminator_custom_form_submit_before_set_fields', [CaptureActionController::class, 'captureActionHandler'], 10, PHP_INT_MAX);
