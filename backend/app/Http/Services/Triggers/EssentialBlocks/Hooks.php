<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\EssentialBlocks\EssentialBlocksController;

Hooks::addAction('eb_form_submit_before_email', [EssentialBlocksController::class, 'essentialBlocksHandler'], 10, PHP_INT_MAX);
