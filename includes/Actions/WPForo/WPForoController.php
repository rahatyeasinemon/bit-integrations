<?php

/**
 * WPForo Integration
 */

namespace BitCode\FI\Actions\WPForo;

use WPForo\Inc\Access_Groups;
use WP_Error;

/**
 * Provide functionality for WPForo integration
 */
class WPForoController
{
    public function authentication()
    {
        if (self::checkedWPForoExists()) {
            wp_send_json_success(true);
        } else {
            wp_send_json_error(
                __(
                    'Please! Install WPForo',
                    'bit-integrations'
                ),
                400
            );
        }
    }

    public static function checkedWPForoExists()
    {
        if (!is_plugin_active('wpforo/wpforo.php')) {
            wp_send_json_error(
                __(
                    'WPForo Plugin is not active or not installed',
                    'bit-integrations'
                ),
                400
            );
        } else {
            return true;
        }
    }

    public function getGroups()
    {
        self::checkedWPForoExists();

        $accessGroups = Access_Groups::get_active();

        if (empty($accessGroups)) {
            wp_send_json_error(
                __(
                    'No access groups found!',
                    'bit-integrations'
                ),
                400
            );
        }

        foreach ($accessGroups as $key => $accessGroup) {
            $groups[] = (object) ['label' => $accessGroup, 'value' => (string) $key];
        }

        wp_send_json_success($groups, 200);
    }

    public function execute($integrationData, $fieldValues)
    {
        self::checkedWPForoExists();

        $integrationDetails = $integrationData->flow_details;
        $integId = $integrationData->id;
        $fieldMap = $integrationDetails->field_map;
        $selectedTask = $integrationDetails->selectedTask;
        $selectedGroup = $integrationDetails->selectedGroup;

        if (empty($fieldMap) || empty($selectedTask) || empty($selectedGroup)) {
            return new WP_Error('REQ_FIELD_EMPTY', __('Fields map, task and group are required for WPForo', 'bit-integrations'));
        }

        $recordApiHelper = new RecordApiHelper($integId);
        $wpforoResponse = $recordApiHelper->execute($fieldValues, $fieldMap, $selectedTask, $selectedGroup);

        if (is_wp_error($wpforoResponse)) {
            return $wpforoResponse;
        }

        return $wpforoResponse;
    }
}
