<?php

/**
 * WPForo Integration
 */

namespace BitCode\FI\Actions\WPForo;

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
            wp_send_json_error(__('WPForo Plugin is not active or not installed', 'bit-integrations'), 400);
        } else {
            return true;
        }
    }

    public function getReputations()
    {
        self::checkedWPForoExists();

        $levels = WPF()->member->levels();

        if (empty($levels)) {
            wp_send_json_error(__('No reputations found!', 'bit-integrations'), 400);
        }

        foreach ($levels as $level) {
            $levelsOptions[] = (object) [
                'label' => 'Level' . ' ' . $level . ' - ' . WPF()->member->rating($level, 'title'),
                'value' => (string) $level
            ];
        }

        wp_send_json_success($levelsOptions, 200);
    }

    public function execute($integrationData, $fieldValues)
    {
        self::checkedWPForoExists();

        $integrationDetails = $integrationData->flow_details;
        $integId = $integrationData->id;
        $fieldMap = $integrationDetails->field_map;
        $selectedTask = $integrationDetails->selectedTask;
        $selectedReputation = $integrationDetails->selectedReputation;

        if (empty($fieldMap) || empty($selectedTask) || ($selectedTask === 'userReputation' && empty($selectedReputation))) {
            return new WP_Error('REQ_FIELD_EMPTY', __('Fields map, task and group are required for WPForo', 'bit-integrations'));
        }

        $recordApiHelper = new RecordApiHelper($integId);
        $wpforoResponse = $recordApiHelper->execute($fieldValues, $fieldMap, $selectedTask, $selectedReputation);

        if (is_wp_error($wpforoResponse)) {
            return $wpforoResponse;
        }

        return $wpforoResponse;
    }
}
