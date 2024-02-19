<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Hooks;
use BitCode\BTCBI\Http\Services\Triggers\EssentialBlocks\EssentialBlocksController;

Hooks::add('eb_form_submit_before_email', [EssentialBlocksController::class, 'essentialBlocksHandler'], 10, PHP_INT_MAX);
