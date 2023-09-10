<?php

namespace BitCode\FI\Triggers\BitForm;

use BitCode\FI\Core\Util\Common;
use BitCode\FI\Core\Util\DateTimeHelper;
use BitCode\FI\Flow\Flow;

final class BitFormController
{
    public static function info()
    {
        $plugin_path = 'bitform/bitforms.php';
        return [
            'name' => 'Bit Form',
            'title' => 'Contact Form Plugin - Fastest Contact Form Builder Plugin for WordPress by Bit Forms.',
            'slug' => $plugin_path,
            'type' => 'form',
            'is_active' => is_plugin_active('bitform/bitforms.php'),
            'activation_url' => wp_nonce_url(self_admin_url('plugins.php?action=activate&amp;plugin=' . $plugin_path . '&amp;plugin_status=all&amp;paged=1&amp;s'), 'activate-plugin_' . $plugin_path),
            'install_url' => wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $plugin_path), 'install-plugin_' . $plugin_path),
            'list' => [
                'action' => 'bitform/get',
                'method' => 'get',
            ],
            'fields' => [
                'action' => 'bitform/get/form',
                'method' => 'post',
                'data' => ['id']
            ],
        ];
    }

    public function getAll()
    {
        if (!is_plugin_active('bitform/bitforms.php')) {
            wp_send_json_error(__('Bit Form is not installed or activated', 'bit-integrations'));
        }

        $forms = \BitCode\BitForm\API\BitForm_Public\BitForm_Public::getForms();
        $all_forms = [];
        foreach ($forms as $form) {
            $all_forms[] = (object)[
                'id' => $form->id,
                'title' => $form->form_name
            ];
        }
        wp_send_json_success($all_forms);
    }

    private static function _getFieldLabel($field)
    {
        if (property_exists($field->settings, 'label') && $field->settings->label) {
            return $field->settings->label;
        } elseif (property_exists($field->settings, 'admin_field_label') && $field->settings->admin_field_label) {
            return $field->settings->admin_field_label;
        } elseif (is_object($field->attributes) && property_exists($field->attributes, 'name') && $field->attributes->name) {
            return $field->attributes->name;
        }
        return '';
    }

    public function get_a_form($data)
    {
        if (empty($data->id)) {
            wp_send_json_error(__('Form doesn\'t exists', 'bit-integrations'));
        }
        $fields = self::fields($data->id);
        if (empty($fields)) {
            wp_send_json_error(__('Form doesn\'t exists any field', 'bit-integrations'));
        }

        $responseData['fields'] = $fields;
        wp_send_json_success($responseData);
    }

    public static function fields($form_id)
    {
        if (!is_plugin_active('bitform/bitforms.php')) {
            return [];
        }

        $fieldDetails = \BitCode\BitForm\API\BitForm_Public\BitForm_Public::getFields($form_id);
        if (empty($fieldDetails)) {
            return [];
        }

        $fields = [];
        foreach ($fieldDetails as $key => $field) {
            if (isset($field->lbl) && !isset($field->txt) && $field->typ !== 'repeater') {
                $name = str_replace(' ', '-', $field->lbl);

                if ($field->typ === 'file-up') {
                    $fields[] = [
                        'name' => strtolower($name) . '-' . $key,
                        'type' => 'file',
                        'label' => $field->lbl
                    ];
                } elseif ($field->typ === 'decision-box') {
                    $name = str_replace(' ', '-', $field->adminLbl);
                    $fields[] = [
                        'name' => strtolower($name) . '-' . $key,
                        'type' => $field->typ,
                        'label' => $field->adminLbl
                    ];
                } else {
                    $fields[] = [
                        'name' => strtolower($name) . '-' . $key,
                        'type' => $field->typ,
                        'label' => $field->lbl
                    ];
                }
            }
        }
        return $fields;
    }

    public static function handle_bitform_submit($formId, $entryId, $formData)
    {
        // error_log(print_r(['entryId:', $entryId], true));
        // error_log(print_r(['formData:', $formData], true));
        // error_log(print_r(['formId:', $formId], true));

        // Flow::execute('BitForm', $form_id, $formData, $flows);

        if (!empty($formId)) {
            $data = [];
            if ($entryId) {
                $formData['entry_id'] = $entryId;
            }
            // foreach ($formData as $fldDetail) {
            //     if (is_array($fldDetail['value'])) {
            //         if (array_key_exists('file', $fldDetail['value'])) {
            //             $data[$fldDetail['name']] = [$fldDetail['value']['file']['file_path']];
            //         } elseif (explode("-", $fldDetail['name'])[0] == 'name') {
            //             if ($fldDetail['name']) {
            //                 $last_dash_position = strrpos($fldDetail['name'], "-");
            //                 $index = substr($fldDetail['name'], $last_dash_position + 1);
            //             }
            //             foreach ($fldDetail['value'] as $nameKey => $nameVal) {
            //                 $data[$nameKey . '-' . $index] = $nameVal;
            //             }
            //         } elseif (explode("-", $fldDetail['name'])[0] == 'address') {
            //             if ($fldDetail['name']) {
            //                 $last_dash_position = strrpos($fldDetail['name'], "-");
            //                 $index = substr($fldDetail['name'], $last_dash_position + 1);
            //             }
            //             foreach ($fldDetail['value'] as $nameKey => $nameVal) {
            //                 $data[$nameKey . '-' . $index] = $nameVal;
            //             }
            //         } else {
            //             $val = $fldDetail['value'];
            //             if (array_key_exists('ampm', $val)) {
            //                 $time = $val['hours'] . ':' . $val['minutes'] . ' ' . $val['ampm'];
            //                 $data[$fldDetail['name']] = $time;
            //             } elseif (array_key_exists('year', $val)) {
            //                 $date = $val['year'] . '-' . $val['month'] . '-' . $val['day'];
            //                 $data[$fldDetail['name']] = $date;
            //             } elseif (array_key_exists('formatting_result', $val)) {
            //                 $data[$fldDetail['name']] = $fldDetail['value']['formatting_result'];
            //             } else {
            //                 $data[$fldDetail['name']] = $fldDetail['value'];
            //             }
            //         }
            //     } else {
            //         if (strtotime($fldDetail['value'])) {
            //             $dateTmp = new DateTime($fldDetail['value']);
            //             $dateFinal = date_format($dateTmp, 'Y-m-d');
            //             $data[$fldDetail['name']] = $dateFinal;
            //         } else {
            //             $data[$fldDetail['name']] = $fldDetail['value'];
            //         }
            //     }
            // }

            if (!empty($formId) && $flows = Flow::exists('BitForm', $formId)) {
                Flow::execute('FormBitForminator', $formId, $formData, $flows);
            }
        }
    }
}
