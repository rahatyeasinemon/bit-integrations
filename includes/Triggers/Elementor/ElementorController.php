<?php

namespace BitCode\FI\Triggers\Elementor;

use WP_Error;
use ReflectionClass;
use BitCode\FI\Flow\Flow;
use BitCode\FI\Core\Util\Helper;

final class ElementorController
{
    public static function info()
    {
        $plugin_path = self::pluginActive('get_name');
        return [
            'name'                  => 'Elementor',
            'title'                 => 'Elementor is the platform web creators choose to build professional WordPress websites, grow their skills, and build their business. Start for free today!',
            'slug'                  => $plugin_path,
            'pro'                   => $plugin_path,
            'type'                  => 'custom_form_submission',
            'is_active'             => is_plugin_active($plugin_path),
            'activation_url'        => wp_nonce_url(self_admin_url('plugins.php?action=activate&amp;plugin=' . $plugin_path . '&amp;plugin_status=all&amp;paged=1&amp;s'), 'activate-plugin_' . $plugin_path),
            'install_url'           => wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $plugin_path), 'install-plugin_' . $plugin_path),
            'documentation_url'     => 'https://bitapps.pro/docs/bit-integrations/trigger/elementor-form-integrations',
            'triggered_entity_id'   => 'elementor_pro/forms/new_record', // Form submission hook act as triggered_entity_id
            'fetch'         => [
                'action'    => 'elementor/test',
                'method'    => 'post',
            ],
            'fetch_remove'  => [
                'action'    => 'elementor/test/remove',
                'method'    => 'post',
            ]
        ];
    }

    public static function pluginActive($option = null)
    {
        if (is_plugin_active('elementor-pro/elementor-pro.php') || is_plugin_active('elementor/elementor.php')) {
            return true;
        }
        return false;
    }

    // public function getAllForms()
    // {
    //     if (!self::pluginActive()) {
    //         wp_send_json_error(__('Elementor Pro is not installed or activated', 'bit-integrations'));
    //     }

    //     $forms = ElementorHelper::all_forms();
    //     foreach ($forms as $form) {
    //         $all_forms[] = (object)[
    //             'id'        => $form['id'] . $form['post_id'],
    //             'title'     => $form['title'],
    //             'post_id'   => $form['post_id']
    //         ];
    //     }
    //     wp_send_json_success($all_forms);
    // }

    // public function getFormFields($data)
    // {
    //     if (!self::pluginActive()) {
    //         wp_send_json_error(__('Elementor Pro is not installed or activated', 'bit-integrations'));
    //     }

    //     if (empty($data->id)) {
    //         wp_send_json_error(__('Form doesn\'t exists', 'bit-integrations'));
    //     }
    //     $fields = self::fields($data);
    //     if (empty($fields)) {
    //         wp_send_json_error(__('Form doesn\'t exists any field', 'bit-integrations'));
    //     }

    //     $responseData['fields'] = $fields;
    //     $responseData['postId'] = $data->postId;
    //     wp_send_json_success($responseData);
    // }

    // public static function fields($data)
    // {
    //     if (empty($data->id)) {
    //         wp_send_json_error(__('Form doesn\'t exists', 'bit-integrations'));
    //     }
    //     $form_id = $data->id;
    //     $post_id = $data->postId;
    //     $fields = [];
    //     $allFormsDetails = ElementorHelper::all_elementor_forms();

    //     foreach ($allFormsDetails as $form) {
    //         if ($form['id'] == substr($form_id, 0, -strlen($post_id)) &&  $form['post_id'] == $post_id) {
    //             foreach ($form['form_fields'] as $field) {
    //                 $type = isset($field->field_type) ? $field->field_type : 'text';
    //                 if ($type === 'upload') {
    //                     $type = 'file';
    //                 }

    //                 $fields[] = [
    //                     'name' => $field->custom_id,
    //                     'type' => $type,
    //                     'label' => $field->field_label,
    //                 ];
    //             }
    //         }
    //     }

    //     if (!empty($fields)) {
    //         return $fields;
    //     }
    //     return false;
    // }

    public function getTestData()
    {
        $testData = get_option('btcbi_elementor_test');

        if ($testData === false) {
            update_option('btcbi_elementor_test', []);
        }
        if (!$testData || empty($testData)) {
            wp_send_json_error(new WP_Error('elementor_test', __('Elementor data is empty', 'bit-integrations')));
        }
        error_log(print_r($testData, true));
        wp_send_json_success($testData);
    }

    public function removeTestData($data)
    {
        if (is_object($data) && property_exists($data, 'reset') && $data->reset) {
            $testData = update_option('btcbi_elementor_test', []);
        } else {
            $testData = delete_option('btcbi_elementor_test');
        }

        if (!$testData) {
            wp_send_json_error(new WP_Error('elementor_test', __('Failed to remove test data', 'bit-integrations')));
        }

        wp_send_json_success(__('Elementor test data removed successfully', 'bit-integrations'));
    }

    public static function handle_elementor_submit($record)
    {
        $recordData = [
            'id'            => $record->get_form_settings('id'),
            'form_post_id'  => $record->get_form_settings('form_post_id'),
            'edit_post_id'  => $record->get_form_settings('edit_post_id'),
            'fields'        => $record->get('fields'),
            'files'         => $record->get('files'),
        ];

        $formData       = ElementorHelper::setFields($recordData);
        $formId         = $recordData['id'];
        $reOrganizeId   = $recordData['id'] . $recordData['form_post_id'];

        if (get_option('btcbi_elementor_test') !== false) {
            update_option('btcbi_elementor_test', [
                'formData'      => $formData,
                'primaryKey'    => [(object) ['key'   => 'id', 'value' => $formId]]
            ]);
        }

        global $wpdb;
        $flows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}btcbi_flow
                WHERE status = true 
                AND triggered_entity = %s 
                AND (triggered_entity_id = %s
                OR triggered_entity_id = %s
                OR triggered_entity_id = %s)",
                'Elementor',
                'elementor_pro/forms/new_record',
                $formId,
                $reOrganizeId
            )
        );

        if (!$flows) {
            return;
        }

        foreach ($flows as $flow) {
            $flowDetails = is_string($flow->flow_details) ? json_decode($flow->flow_details) : $flow->flow_details;

            if (!isset($flowDetails->primaryKey) && ($flow->triggered_entity_id == $formId || $flow->triggered_entity_id == $reOrganizeId)) {
                $data   = [];
                foreach ($record->get('fields') as $field) {
                    if ($field['type'] == 'upload') {
                        $data[$field['id']] = explode(',', $field['value']);
                    } else {
                        $data[$field['id']] = $field['value'];
                    }
                }

                Flow::execute('Elementor', $flows[0]->triggered_entity_id, $data, array($flow));
                continue;
            }

            if (!is_array($flowDetails->primaryKey)) {
                continue;
            }
            $isPrimaryKeysMatch = true;
            foreach ($flowDetails->primaryKey as $primaryKey) {
                $primaryKeyValue = Helper::extractValueFromPath($recordData, $primaryKey->key);

                if ($primaryKey->value != $primaryKeyValue) {
                    $isPrimaryKeysMatch = false;
                    break;
                }
            }

            if ($isPrimaryKeysMatch) {
                $data = array_column($formData, 'value', 'name');
                Flow::execute('Elementor', $flows[0]->triggered_entity_id, $data, array($flow));
            }
        }

        return ['type' => 'success'];
        // $data   = [];
        // $fields = $record->get('fields');
        // foreach ($fields as $field) {
        //     if ($field['type'] == 'upload') {
        //         $data[$field['id']] = explode(',', $field['raw_value']);
        //     } else {
        //         $data[$field['id']] = $field['raw_value'];
        //     }
        // }

        // Flow::execute('Elementor', $flows[0]->triggered_entity_id, $data, $flows);
    }
}
