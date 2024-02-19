<?php

if (!defined('ABSPATH')) {
    exit;
}


use BitCode\BTCBI\Util\Hooks;
use BitCode\BTCBI\Http\Services\Triggers\NF\NFController;

Hooks::add('ninja_forms_after_submission', [NFController::class, 'ninja_forms_after_submission'], 10, 1);
