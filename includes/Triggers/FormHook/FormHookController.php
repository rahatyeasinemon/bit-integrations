<?php

namespace BitCode\FI\Triggers\FormHook;

use WP_Error;
use BitCode\FI\Flow\Flow;

class FormHookController
{
    protected static $formHookIntegrationsList = [
        'Webhook',
        'Spectra',
    ];
    // public static function info()
    // {
    //     return [
    //         'name' => 'Form Hook',
    //         'title' => 'Get callback data through an URL',
    //         'type' => 'form_hook',
    //         'is_active' => true
    //     ];
    // }

    public function getTestData()
    {
        $testData = get_option('btcbi_form_hook_test_uagb_form_success');

        if ($testData === false) {
            update_option('btcbi_form_hook_test_uagb_form_success', []);
        }
        if (!$testData || empty($testData)) {
            wp_send_json_error(new WP_Error('formHook_test', __('FormHook data is empty', 'bit-integrations')));
        }
        wp_send_json_success(['formHook' => $testData]);
    }

    public static function formHookHandler(...$args)
    {
        if (get_option('btcbi_form_hook_test_uagb_form_success') !== false) {
            update_option('btcbi_form_hook_test_uagb_form_success', $args);
        }
        if ($flows = Flow::exists(self::$formHookIntegrationsList, 'FormHook')) {

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
                    // var_dump('fdasjkwfhd');
                    // die;
                    Flow::execute('FormHook', current_action(), $formatedData, array($flow));
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
                wp_send_json_error(new WP_Error('Action Hook', __('Index out of bounds or invalid', 'bit-integrations')));
            }
            return self::extractValueFromPath($data[$currentPart], $parts);
        }

        if (is_object($data)) {
            if (!property_exists($data, $currentPart)) {
                wp_send_json_error(new WP_Error('Action Hook', __('Invalid path', 'bit-integrations')));
            }
            return self::extractValueFromPath($data->$currentPart, $parts);
        }

        wp_send_json_error(new WP_Error('Action Hook', __('Invalid path', 'bit-integrations')));
    }
}
