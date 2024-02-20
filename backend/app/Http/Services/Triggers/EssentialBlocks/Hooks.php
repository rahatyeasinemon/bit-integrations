<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\EssentialBlocks\EssentialBlocksController;

Hooks::add('eb_form_submit_before_email', [EssentialBlocksController::class, 'essentialBlocksHandler'], 10, PHP_INT_MAX);
