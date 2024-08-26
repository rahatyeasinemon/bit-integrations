<?php

namespace BitCode\FI\Core\Util;

use BitCode\FI\Flow\FlowController;

class StoreInCache
{
    public static function getActiveFlowEntities($refresh = false)
    {
        return self::maybeFetchActiveFlowEntities('bit_integrations_active_trigger_entities', $refresh);
    }

    public static function getFallbackFlowEntities($refresh = false)
    {
        return self::maybeFetchActiveFlowEntities('bit_integrations_fallback_trigger_entities', $refresh);
    }

    public static function setTransient($key, $value, $expiration)
    {
        if (empty($key) || empty($value)) {
            return false;
        }
        set_transient($key, $value, $expiration);
    }

    public static function getTransientData($key)
    {
        if (empty($key)) {
            return false;
        }
        $transientData = get_transient($key);
        if (empty($transientData)) {
            return false;
        }

        return $transientData;
    }

    private static function saveFallbackFlowEntities($integrations)
    {
        $entities = [];
        $excludedEntities = ['CustomTrigger', 'ActionHook', 'BitForm', 'CF7', 'WC', 'WPF', 'Breakdance', 'Elementor'];

        foreach ($integrations as $flow) {
            if (!\in_array($flow->triggered_entity, $excludedEntities)) {
                $flow->flow_details = \is_string($flow->flow_details) ? json_decode($flow->flow_details) : $flow->flow_details;

                if (empty($flow->flow_details->pro_integ_v)) {
                    $entities[$flow->triggered_entity] = $flow->triggered_entity;
                }
            }
        }

        if (!empty($entities)) {
            self::setTransient('bit_integrations_fallback_trigger_entities', $entities, DAY_IN_SECONDS);

            return $entities;
        }

        return false;
    }

    private static function saveActiveFlowEntities($integrations)
    {
        $activeTriggerLists = array_unique(array_column($integrations, 'triggered_entity'));

        self::setTransient('bit_integrations_active_trigger_entities', $activeTriggerLists, DAY_IN_SECONDS);

        return $activeTriggerLists;
    }

    private static function maybeFetchActiveFlowEntities($key, $refresh)
    {
        if (!$refresh && ($triggerEntities = self::getTransientData($key))) {
            return $triggerEntities;
        }

        $integrationHandler = new FlowController();
        $integrations = $integrationHandler->get(
            ['status' => 1],
            [
                'triggered_entity',
                'flow_details',
                'status',
            ]
        );

        $activeFlowEntities = static::saveActiveFlowEntities($integrations);
        $fallbackFlowEntities = null;

        if (!Helper::isProActivate()) {
            $fallbackFlowEntities = static::saveFallbackFlowEntities($integrations);
        }

        return $key === 'bit_integrations_active_trigger_entities' ? $activeFlowEntities : $fallbackFlowEntities ?? [];
    }
}
