<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Hooks;
use BitCode\BTCBI\Http\Services\Triggers\Divi\DiviController;

Hooks::add('et_pb_contact_form_submit', [DiviController::class, 'handle_divi_submit'], 10, 3);
