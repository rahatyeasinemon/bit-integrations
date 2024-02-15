<?php
if (!defined('ABSPATH')) {
    exit;
}


use BitCode\BTCBI\Core\Util\Hooks;
use BitCode\BTCBI\Triggers\NF\NFController;

Hooks::add('ninja_forms_after_submission', [NFController::class, 'ninja_forms_after_submission'], 10, 1);
