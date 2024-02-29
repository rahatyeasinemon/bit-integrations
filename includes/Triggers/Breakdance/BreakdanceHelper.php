<?php

namespace BitCode\FI\Triggers\Breakdance;

class BreakdanceHelper
{
    public static function setFields($data, $form)
    {
        $allFields = [
            ['name' => self::findKeyPath($data, 'formId'), 'type' => 'text', 'label' => "Form Id ({$data['formId']})"],
            ['name' => self::findKeyPath($data, 'postId'), 'type' => 'text', 'label' => "Post Id ({$data['postId']})"]
        ];

        $formFields = [];
        foreach ($form as $field) {
            $formFields[$field['name']] = $field;
        }

        foreach ($data['fields'] as $key => $value) {
            $formKey = "fields[$key]";
            if (isset($formFields[$formKey])) {
                $allFields[] = [
                    'name'  => self::findKeyPath($data, $key),
                    'type'  => $formFields[$formKey]['type'],
                    'label' => $formFields[$formKey]['label'] . ' (' . $value . ')'
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
}