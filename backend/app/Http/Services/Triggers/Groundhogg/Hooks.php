<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\Groundhogg\GroundhoggController;

// Hooks::addAction('groundhogg/db/post_insert/contact', [GroundhoggController::class, 'handle_groundhogg_submit'], 10, 2);
Hooks::addAction('groundhogg/contact/post_create', [GroundhoggController::class, 'handle_groundhogg_submit'], 10, 3);
Hooks::addAction('groundhogg/contact/tag_applied', [GroundhoggController::class, 'tagApplied'], 10, 2);
Hooks::addAction('groundhogg/contact/tag_removed', [GroundhoggController::class, 'tagRemove'], 10, 2);
