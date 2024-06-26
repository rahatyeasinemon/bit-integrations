<?php

/**
 * SureMembers Integration
 */

namespace BitCode\FI\Actions\SureMembers;

use WP_Error;

/**
 * Provide functionality for SureMembers integration
 */
class SureMembersController
{
    public function authentication()
    {
        if (self::checkedSureMembersExists()) {
            wp_send_json_success(true);
        } else {
            wp_send_json_error(
                __(
                    'Please! Install SureMembers',
                    'bit-integrations'
                ),
                400
            );
        }
    }

    public static function checkedSureMembersExists()
    {
        error_log(print_r(['plugin check' => is_plugin_active('suremembers/suremembers.php')], true));

        if (!is_plugin_active('suremembers/suremembers.php')) {
            wp_send_json_error(
                __(
                    'SureMembers Plugin is not active or not installed',
                    'bit-integrations'
                ),
                400
            );
        } else {
            return true;
        }
    }

    public function execute($integrationData, $fieldValues)
    {
        self::checkedSureMembersExists();

        $integrationDetails = $integrationData->flow_details;
        $integId = $integrationData->id;
        $fieldMap = $integrationDetails->field_map;
        $selectedLists = $integrationDetails->selectedLists;

        if (empty($fieldMap)) {
            return new WP_Error('REQ_FIELD_EMPTY', __('fields map are required for SureMembers', 'bit-integrations'));
        }

        $recordApiHelper = new RecordApiHelper($integId);
        $sureMembersResponse = $recordApiHelper->execute($fieldValues, $fieldMap, $selectedLists);

        if (is_wp_error($sureMembersResponse)) {
            return $sureMembersResponse;
        }

        return $sureMembersResponse;
    }
}
