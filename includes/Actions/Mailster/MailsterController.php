<?php

/**
 * Mailster Integration
 */

namespace BitCode\FI\Actions\Mailster;

use MailsterBlockForms;
use WP_Error;

/**
 * Provide functionality for Mailster integration
 */
class MailsterController
{
    public function authentication()
    {
        if (self::checkedMailsterExists()) {
            wp_send_json_success(true);
        } else {
            wp_send_json_error(
                __(
                    'Please! Install Mailster',
                    'bit-integrations'
                ),
                400
            );
        }
    }

    public static function checkedMailsterExists()
    {
        if (!is_plugin_active('mailster/mailster.php')) {
            wp_send_json_error(
                __(
                    'Mailster Plugin is not active or not installed',
                    'bit-integrations'
                ),
                400
            );
        } else {
            return true;
        }
    }

    public function getMailsterFields()
    {
        self::checkedMailsterExists();

        $mailsterBlockForms = new MailsterBlockForms();
        $fields = $mailsterBlockForms->get_fields();

        if (empty($fields) || is_wp_error($fields)) {
            wp_send_json_error(__('Fields fetching failed!', 'bit-integrations'), 400);
        }

        $formattedFields = [];

        foreach ($fields as $field) {
            if ($field['id'] != 'submit') {
                if (isset($field['values']) && !empty($field['values'][0])) {
                    $options = $field['values'];
                } else {
                    $options = [];
                }

                $formattedFields[] = (object) [
                    'key'      => $field['id'],
                    'label'    => $field['name'],
                    'required' => $field['id'] === 'email' ? true : false,
                    'options'  => $options
                ];
            }
        }

        wp_send_json_success($formattedFields, 200);
    }

    public function execute($integrationData, $fieldValues)
    {
        self::checkedMailsterExists();

        $integrationDetails = $integrationData->flow_details;
        $integId = $integrationData->id;
        $fieldMap = $integrationDetails->field_map;
        $selectedLists = $integrationDetails->selectedLists;

        if (empty($fieldMap)) {
            return new WP_Error('REQ_FIELD_EMPTY', __('fields map are required for Mailster', 'bit-integrations'));
        }

        $recordApiHelper = new RecordApiHelper($integId);
        $mailsterResponse = $recordApiHelper->execute($fieldValues, $fieldMap, $selectedLists);

        if (is_wp_error($mailsterResponse)) {
            return $mailsterResponse;
        }

        return $mailsterResponse;
    }
}
