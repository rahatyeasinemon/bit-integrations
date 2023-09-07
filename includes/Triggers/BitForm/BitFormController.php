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
            if (isset($field->lbl)) {
                // if (property_exists($field, 'raw') && array_key_exists('multiple_name', $field->raw) && $field->raw['multiple_name'] && $field->raw['type'] == 'name') {
                //     $tmpName = $field->raw;
                //     $names = [];
                //     foreach ($tmpName as $key => $val) {
                //         if ($key == 'element_id' && $val) {
                //             $last_dash_position = strrpos($val, "-");
                //             $index = substr($val, $last_dash_position + 1);
                //         }
                //         if (($key == 'fname' || $key == 'lname' || $key == 'mname' || $key == 'prefix') && $val) {
                //             if ($key == 'fname') {
                //                 $names['first-name-' . $index] = 'First Name-' . $index;
                //             } elseif ($key == 'lname') {
                //                 $names['last-name-' . $index] = 'Last Name-' . $index;
                //             } elseif ($key == 'mname') {
                //                 $names['middle-name-' . $index] = 'Middle Name-' . $index;
                //             } elseif ($key == 'prefix') {
                //                 $names['prefix'] = 'Name Prefix';
                //             }
                //         }
                //     }

                //     foreach ($names as $key => $value) {
                //         $fields[] = [
                //             'name' => $key,
                //             'type' => 'text',
                //             'label' => $value,
                //         ];
                //     }
                // } elseif (property_exists($field, 'raw') && $field->raw['type'] == 'address' && is_array(($field->raw))) {
                //     $all_fields = $field->raw;
                //     $address = [
                //         'street_address' => 'Street Address',
                //         'city' => 'Address City',
                //         'state' => 'Address State',
                //         'zip' => 'Address Zip',
                //         'country' => 'Address Country',
                //         'address_line' => 'Address Line',
                //     ];
                //     $keys = ['street_address', 'address_city', 'address_state', 'address_zip', 'address_country', 'address_line'];
                //     foreach ($all_fields as $key => $value) {
                //         if (in_array($key, $keys)) {
                //             if (array_key_exists($key, $all_fields) && $all_fields[$key]) {
                //                 if ($key != 'street_address' && $key != 'address_line') {
                //                     $key = substr($key, 8);
                //                 }
                //                 if ($key == 'element_id' && $value) {
                //                     $last_dash_position = strrpos($value, "-");
                //                     $index = substr($value, $last_dash_position + 1);
                //                 }
                //                 $fields[] = [
                //                     'name' => $key . '-' . $index,
                //                     'type' => 'text',
                //                     'label' => $address[$key] . '-' . $index,
                //                 ];
                //             }
                //         }
                //     }
                // } else {
                //     $type = $field->type;
                //     if ($type === 'upload') {
                //         $type = 'file';
                //     }
                //     if ($field->slug) {
                //         $last_dash_position = strrpos($field->slug, "-");
                //         $index = substr($field->slug, $last_dash_position + 1);
                //     }
                //     $fields[] = [
                //         'name' => $field->slug,
                //         'type' => $type,
                //         'label' => $field->field_label . '-' . $index,
                //     ];
                // }

                $fields[] = [
                    'name' => $key,
                    'type' => $field->typ,
                    'label' => $field->lbl
                ];
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
