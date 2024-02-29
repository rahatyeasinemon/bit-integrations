<?php

namespace BitCode\FI\Triggers\Breakdance;

use WP_Error;

class BreakdanceHelper
{
    public static function setFields($data, $form)
    {
        $allFields = [
            ['name' => self::findKeyPath($data, 'formId'), 'type' => 'text', 'label' => "Form Id ({$data['formId']})", 'value' => $data['formId']],
            ['name' => self::findKeyPath($data, 'postId'), 'type' => 'text', 'label' => "Post Id ({$data['postId']})", 'value' => $data['postId']]
        ];

        $formFields = [];
        foreach ($form as $field) {
            $formFields[$field['name']] = $field;
        }

        foreach ($data['fields'] as $key => $value) {
            $formKey = "fields[$key]";
            if (isset($formFields[$formKey])) {
                $label = strlen($value) > 20 ? substr($value, 0, 20) . '...' : $value;

                $allFields[] = [
                    'name'  => self::findKeyPath($data, $key),
                    'type'  => $formFields[$formKey]['type'],
                    'label' => $formFields[$formKey]['label'] . ' (' . $label . ')',
                    'value' => $formFields[$formKey]['type'] != 'file' ? $value : explode(',', $value)
                ];
            }
        }

        return $allFields;
    }

    public static function findKeyPath(array $data, string $searchKey, array $currentPath = [])
    {
        foreach ($data as $key => $value) {
            $path   = $currentPath;
            $path[] = $key;

            if (is_object($value)) {
                $value = (array) $value;
            }
            if ($key === $searchKey) {
                return implode('.', $path);
            }
            if (is_array($value)) {
                $foundPath = self::findKeyPath($value, $searchKey, $path);
                if ($foundPath) {
                    return $foundPath;
                }
            }
        }

        return null;
    }

    public static function extractValueFromPath($data, $path)
    {
        $parts = is_array($path) ? $path : explode('.', $path);
        if (count($parts) === 0) {
            return $data;
        }

        $currentPart = array_shift($parts);
        if (is_array($data)) {
            if (!isset($data[$currentPart])) {
                wp_send_json_error(new WP_Error('Breakdance', __('Index out of bounds or invalid', 'bit-integrations')));
            }
            return self::extractValueFromPath($data[$currentPart], $parts);
        }

        if (is_object($data)) {
            if (!property_exists($data, $currentPart)) {
                wp_send_json_error(new WP_Error('Breakdance', __('Invalid path', 'bit-integrations')));
            }
            return self::extractValueFromPath($data->$currentPart, $parts);
        }

        wp_send_json_error(new WP_Error('Breakdance', __('Invalid path', 'bit-integrations')));
    }
}
