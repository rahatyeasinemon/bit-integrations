<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Triggers\MetaBox\MetaBoxController;
use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;

//* METABOX SUBMITTED ACTION HOOK*//
Hooks::addAction('rwmb_frontend_after_save_post', [MetaBoxController::class, 'handle_metabox_submit'], 10, 1);
//* METABOX SUBMITTED ACTION HOOK*//
