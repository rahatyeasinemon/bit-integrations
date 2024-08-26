<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\FI\Core\Util\Hooks;
use BitCode\FI\Core\Util\Helper;
use BitCode\FI\Flow\FlowController;
use BitCode\FI\Triggers\ActionHook\ActionHookController;

if (!Helper::isProActivate()) {
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
}
