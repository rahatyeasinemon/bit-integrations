<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\FI\Core\Util\Hooks;
use BitCode\FI\Flow\FlowController;
use BitCode\FI\Triggers\CaptureAction\CaptureActionController;

global $wpdb;
$hook = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT option_name
            FROM $wpdb->options
            WHERE option_name LIKE %s
            ORDER BY option_id DESC
            LIMIT 1",
        'btcbi_capture_action_test\_%'
    )
);

if (!empty($hook) && isset($hook[0]->option_name)) {
    Hooks::add(str_replace('btcbi_capture_action_test_', '', $hook[0]->option_name), [CaptureActionController::class, 'captureActionHandler'], 10, PHP_INT_MAX);
}

$flowController = new FlowController();
$flows = $flowController->get(
    [
        'triggered_entity' => 'CaptureAction',
        'status' => 1,
    ],
    ['triggered_entity_id']
);

if (!is_wp_error($flows)) {
    foreach ($flows as  $flow) {
        if (isset($flow->triggered_entity_id)) {
            Hooks::add($flow->triggered_entity_id, [CaptureActionController::class, 'handle'], 10, PHP_INT_MAX);
        }
    }
}
