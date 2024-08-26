<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\FI\Core\Util\Hooks;
use BitCode\FI\Core\Util\Helper;
use BitCode\FI\Core\Util\StoreInCache;
use BitCode\FI\Triggers\FallbackTrigger\FallbackHooks;
use BitCode\FI\Triggers\FallbackTrigger\FallbackTriggerController;

if (!Helper::isProActivate()) {
    $storeInCacheInstance = new StoreInCache();
    $integrations = $storeInCacheInstance::getTransientData('activeCurrentIntegrations') ?: $storeInCacheInstance::getActiveIntegration() ?: [];
    $entities = array_unique(array_column($integrations, 'triggered_entity'));

    foreach (FallbackHooks::$triggerHookList as $trigger) {
        if (in_array($trigger['entity'], $entities)) {
            $flowData = array_filter($integrations, function ($flow) use ($trigger) {
                if ($flow->triggered_entity == $trigger['entity']) {
                    $flow->flow_details = is_string($flow->flow_details) ? json_decode($flow->flow_details) : $flow->flow_details;

                    return !isset($flow->flow_details->pro_integ_v);
                }

                return false;
            });

            if (!empty($flowData)) {
                $hookFunction = $trigger['isFilterHook'] ? 'filter' : 'add';
                Hooks::$hookFunction($trigger['hook'], [FallbackTriggerController::class, 'triggerFallbackHandler'], $trigger['priority'], PHP_INT_MAX);
            }
        }
    }
}
