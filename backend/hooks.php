<?php

/***
 * If try to direct access  plugin folder it will Exit
 **/

use BitApps\BTCBI\Util\Activation;
use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;

if (!defined('ABSPATH')) {
    exit;
}

// Hooks::addAction('wp_insert_site', [Activation::class, 'handle_new_site'], 10, 1);
Hooks::addAction('wp_initialize_site', [Activation::class, 'handle_new_site'], 200, 1);
