<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\FI\Core\Util\Hooks;
use BitCode\FI\Triggers\CaptureAction\CaptureActionController;

global $wpdb;
$post_metas = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT option_name
            FROM $wpdb->options
            WHERE option_name LIKE %s
            ORDER BY option_id DESC
            LIMIT 1",
        'btcbi_capture_action_test\_%'
    )
);

$action_hook = str_replace('btcbi_capture_action_test_', '', $post_metas[0]->option_name);
Hooks::add($action_hook, [CaptureActionController::class, 'captureActionHandler'], 10, PHP_INT_MAX);
