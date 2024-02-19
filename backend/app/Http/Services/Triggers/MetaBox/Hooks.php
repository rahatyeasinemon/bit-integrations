<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Triggers\MetaBox\MetaBoxController;
use BitCode\BTCBI\Util\Hooks;

//* METABOX SUBMITTED ACTION HOOK*//
Hooks::add('rwmb_frontend_after_save_post', [MetaBoxController::class, 'handle_metabox_submit'], 10, 1);
//* METABOX SUBMITTED ACTION HOOK*//
