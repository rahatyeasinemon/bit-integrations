<?php

namespace BitCode\FI\Triggers\Elementor;

class ElementorHelper
{
    public static function setFields($formData)
    {
        $allFields  = [
            ['name' => 'id', 'type' => 'text', 'label' => "Form Id ({$formData['id']})", 'value' => $formData['id']],
            ['name' => 'form_post_id', 'type' => 'text', 'label' => "Form Post Id ({$formData['form_post_id']})", 'value' => $formData['form_post_id']],
            ['name' => 'edit_post_id', 'type' => 'text', 'label' => "Edit Post Id ({$formData['edit_post_id']})", 'value' => $formData['edit_post_id']],
        ];

        // Process fields data
        foreach ($formData['fields'] as $key => $field) {
            if ($field['type'] != 'upload') {
                $labelValue = strlen($field['value']) > 20 ? substr($field['value'], 0, 20) . '...' : $field['value'];

                $allFields[] = [
                    'name'  => "fields.$key.value",
                    'type'  => $field['type'],
                    'label' => $field['title'] . ' (' . $labelValue . ')',
                    'value' => $field['value']
                ];
            }
        }

        // Process files data
        foreach ($formData['files'] as $key => $file) {
            if (!empty($file)) {
                $fieldTitle = isset($formData['fields'][$key]['title']) ? $formData['fields'][$key]['title'] : 'File';

                $allFields[] = [
                    'name'  => "files.$key.url",
                    'type'  => 'file',
                    'label' => $fieldTitle,
                    'value' => $file['url']
                ];
            }
        }

        return $allFields;
    }
}