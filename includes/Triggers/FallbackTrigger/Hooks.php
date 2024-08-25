<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\FI\Core\Util\Helper;
use BitCode\FI\Core\Util\Hooks;
use BitCode\FI\Core\Util\StoreInCache;
use BitCode\FI\Triggers\FallbackTrigger\FallbackHooks;
use BitCode\FI\Triggers\FallbackTrigger\FallbackTriggerController;

if (!Helper::isProActivate()) {
    $storeInCacheInstance = new StoreInCache();
    $integrations = $storeInCacheInstance::getTransientData('activeCurrentIntegrations');

    if (empty($integrations)) {
        $integrations = $storeInCacheInstance::getActiveIntegration();
    }
    if (!$integrations) {
        $integrations = [];
    }

    $entities = array_unique(array_column($integrations, 'triggered_entity'));
    $activeIntegrations = array_filter(FallbackHooks::$triggerHookList, function ($trigger) use ($entities) {
        return in_array($trigger['entity'], $entities);
    });

    foreach ($activeIntegrations as $integration) {
        $flowData = array_filter($integrations, function ($flow) use ($integration) {
            if ($flow->triggered_entity === $integration['entity']) {
                $flow->flow_details = is_string($flow->flow_details) ? json_decode($flow->flow_details) : $flow->flow_details;

                return !isset($flow->flow_details->pro_integ_v);
            }

            return false;
        });

        if (!empty($flowData)) {
            $hookFunction = $integration['isFilterHook'] ? 'filter' : 'add';
            Hooks::$hookFunction($integration['hook'], [FallbackTriggerController::class, 'triggerFallbackHandler'], $integration['priority'], PHP_INT_MAX);
        }
    }
}
