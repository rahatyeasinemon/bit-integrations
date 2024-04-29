<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\FI\Core\Hooks\FallbackHooks;
use BitCode\FI\Core\Util\Hooks;
use BitCode\FI\Flow\FlowController;
use BitCode\FI\Triggers\ActionHook\ActionHookController;

global $wpdb;
$hook = $wpdb->get_results(
    "SELECT option_name
            FROM {$wpdb->options}
            WHERE option_name LIKE 'btcbi_action_hook_test\_%'
            ORDER BY option_id DESC
            LIMIT 1"
);

if (!empty($hook) && isset($hook[0]->option_name)) {
    Hooks::add(str_replace('btcbi_action_hook_test_', '', $hook[0]->option_name), [ActionHookController::class, 'actionHookHandler'], 10, PHP_INT_MAX);
}

$flowController = new FlowController();
$flows = $flowController->get(
    [
        'triggered_entity' => 'ActionHook',
        'status'           => 1,
    ],
    ['triggered_entity_id']
);

if (!is_wp_error($flows)) {
    foreach ($flows as $flow) {
        if (isset($flow->triggered_entity_id)) {
            Hooks::add($flow->triggered_entity_id, [ActionHookController::class, 'handle'], 10, PHP_INT_MAX);
        }
    }
}

if (!function_exists('btcbi_pro_activate_plugin')) {

    foreach (FallbackHooks::$triggerHookList as  $hook) {
        if (!$hook['isFilterHook']) {
            Hooks::add($hook['hook'], [ActionHookController::class, 'triggerFallbackHandler'], $hook['priority'], PHP_INT_MAX);
        } else {
            Hooks::filter($hook['hook'], [ActionHookController::class, 'triggerFallbackHandler'], $hook['priority'], PHP_INT_MAX);
        }
    }
}
