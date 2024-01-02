<?php

namespace BitCode\FI\Triggers\CaptureAction;

use WP_Error;
use BitCode\FI\Flow\Flow;

class CaptureActionController
{
    public static function info()
    {
        return [
            'name' => 'CaptureAction',
            'title' => 'Get callback data through an URL',
            'type' => 'capture_action',
            'is_active' => true
        ];
    }

    public function getTestData($data)
    {
        $missing_field = null;

        if (!property_exists($data, 'hook_id')) {
            $missing_field = is_null($missing_field) ? 'CaptureAction ID' : $missing_field . ', CaptureAction ID';
        }
        if (!is_null($missing_field)) {
            wp_send_json_error(sprintf(__('%s can\'t be empty or need to be valid', 'bit-integrations'), $missing_field));
        }

        $testData = get_option('btcbi_capture_action_test_' . $data->hook_id);
        if ($testData === false) {
            update_option('btcbi_capture_action_test_' . $data->hook_id, []);
        }
        if (!$testData || empty($testData)) {
            wp_send_json_error(new WP_Error('captureAction_test', __('CaptureAction data is empty', 'bit-integrations')));
        }
        wp_send_json_success(['captureAction' => $testData]);
    }

    public static function captureActionHandler(...$args)
    {
        if (get_option('btcbi_capture_action_test_' . current_action()) !== false) {
            update_option('btcbi_capture_action_test_' . current_action(), $args);
        }
        return rest_ensure_response(['status' => 'success']);
    }

    public function removeTestData($data)
    {
        $missing_field = null;
        if (!property_exists($data, 'hook_id') && !empty($data->hook_id)) {
            $missing_field = is_null($missing_field) ? 'CaptureAction ID' : $missing_field . ', CaptureAction ID';
        }
        if (!is_null($missing_field)) {
            wp_send_json_error(sprintf(__('%s can\'t be empty or need to be valid', 'bit-integrations'), $missing_field));
        }

        if (property_exists($data, 'reset') && $data->reset) {
            $testData = update_option('btcbi_capture_action_test_' . $data->hook_id, []);
        } else {
            $testData = delete_option('btcbi_capture_action_test_' . $data->hook_id);
        }
        if (!$testData) {
            wp_send_json_error(new WP_Error('captureAction_test', __('Failed to remove test data', 'bit-integrations')));
        }
        wp_send_json_success(__('CaptureAction test data removed successfully', 'bit-integrations'));
    }

    public static function handle(...$args)
    {
        if ($flows = Flow::exists('CaptureAction', current_action())) {
            foreach ($flows as $flow) {
                $flowDetails = json_decode($flow->flow_details);

                if (!isset($flowDetails->primaryKey)) {
                    continue;
                }

                $primaryKeyValue = self::extractValueFromPath($args, $flowDetails->primaryKey->key);
                if ($flowDetails->primaryKey->value === $primaryKeyValue) {
                    $fieldKeys      = [];
                    $formatedData   = [];

                    if ($flowDetails->body->data && is_array($flowDetails->body->data)) {
                        $fieldKeys = array_map(function ($field) use ($args) {
                            return $field->key;
                        }, $flowDetails->body->data);
                    } elseif (isset($flowDetails->field_map) && is_array($flowDetails->field_map)) {
                        $fieldKeys = array_map(function ($field) use ($args) {
                            return $field->formField;
                        }, $flowDetails->field_map);
                    }

                    foreach ($fieldKeys as $key) {
                        $formatedData[$key] = self::extractValueFromPath($args, $key);
                    }

                    Flow::execute('CaptureAction', current_action(), $formatedData, array($flow));
                }
            }
        }

        return rest_ensure_response(['status' => 'success']);
    }

    private static function extractValueFromPath($data, $path)
    {
        $parts = is_array($path) ? $path : explode('.', $path);
        if (count($parts) === 0) {
            return $data;
        }

        $currentPart = array_shift($parts);
        if (is_array($data)) {
            if (!isset($data[$currentPart])) {
                wp_send_json_error(new WP_Error('capture Action', __('Index out of bounds or invalid', 'bit-integrations')));
            }
            return self::extractValueFromPath($data[$currentPart], $parts);
        }

        if (is_object($data)) {
            if (!property_exists($data, $currentPart)) {
                wp_send_json_error(new WP_Error('capture Action', __('Invalid path', 'bit-integrations')));
            }
            return self::extractValueFromPath($data->$currentPart, $parts);
        }

        wp_send_json_error(new WP_Error('capture Action', __('Invalid path', 'bit-integrations')));
    }
}
