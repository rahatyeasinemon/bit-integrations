<?php

namespace BitCode\FI\Triggers\Brizy;

use BitCode\FI\Core\Util\Common;
use BitCode\FI\Flow\Flow;
use BitCode\FI\Log\LogHandler;

final class BrizyController
{
    public static function info()
    {
        $plugin_path = 'brizy/brizy.php';
        return [
            'name' => 'Brizy',
            'title' => 'Brizy is the platform web creators choose to build professional WordPress websites, grow their skills, and build their business. Start for free today!',
            'slug' => $plugin_path,
            'pro' => $plugin_path,
            'type' => 'form',
            'is_active' => is_plugin_active($plugin_path),
            'activation_url' => wp_nonce_url(self_admin_url('plugins.php?action=activate&amp;plugin=' . $plugin_path . '&amp;plugin_status=all&amp;paged=1&amp;s'), 'activate-plugin_' . $plugin_path),
            'install_url' => wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $plugin_path), 'install-plugin_' . $plugin_path),
            'list' => [
                'action' => 'brizy/get',
                'method' => 'get',
            ],
            'fields' => [
                'action' => 'brizy/get/form',
                'method' => 'post',
                'data' => ['id']
            ],
        ];
    }

    public static function handle_brizy_submit($fields, $form)
    {
        if (!method_exists($form, 'getId')) {
            return $fields;
        }
        $form_id = $form->getId();
        $flows = Flow::exists('Brizy', $form_id);
        try {
            if (!$flows) {
                return $fields;
            }
            $data = [];
            $AllFields = $fields;
            foreach ($AllFields as $element) {
                if ($element->type == 'FileUpload' && !empty($element->value)) {
                    $upDir = wp_upload_dir();
                    $files = $element->value;
                    $value = [];
                    $newFileLink = Common::filePath($files);
                    $data[$element->name] = $newFileLink;
                } elseif ($element->type == 'checkbox') {
                    $value = explode(',', $element->value);
                    $data[$element->name] = $value;
                } else {
                    $data[$element->name] = $element->value;
                }
            }
            Flow::execute('Brizy', $form_id, $data, $flows);
        } catch (\Exception $e) {
            foreach ($flows as $flowData) {
                $integrationId = $flowData->id;
                $msg = $e->getMessage();

                LogHandler::save($integrationId, json_encode(['type' => 'error', 'type_name' => 'error']), 'error', json_encode($msg));
            }
        }
        return $fields;
    }

    public function getAllForms()
    {
        if (!is_plugin_active('brizy/brizy.php')) {
            wp_send_json_error(__('Brizy Pro is not installed or activated', 'bit-integrations'));
        }
        //Brizy get form list
        $posts      = self::getBrizyPosts();
        $all_forms  = [];

        foreach ($posts as $forms) {
            $index          = 0;
            $post_meta      = get_post_meta($forms->ID, 'brizy');
            $tamplate_form  = json_decode(base64_decode($post_meta[0]['brizy-post']['editor_data']));

            if (isset($tamplate_form->items)) {
                foreach ($tamplate_form->items as $form) {
                    $index += 1;
                    $all_forms[] = (object)[
                        'id'                => self::get_tamplate_form_id($form->value->items),
                        'title'             => $forms->post_title . '->' . $index,
                        'post_id'           => $forms->ID,
                    ];
                }
            }
        }
        wp_send_json_success($all_forms);
    }

    public function getFormFields($data)
    {
        if (!is_plugin_active('brizy/brizy.php')) {
            wp_send_json_error(__('Brizy Pro is not installed or activated', 'bit-integrations'));
        }
        if (empty($data->id) && empty($data->postId)) {
            wp_send_json_error(__('Form doesn\'t exists', 'bit-integrations'));
        }

        $fields = self::fields($data);
        if (empty($fields)) {
            wp_send_json_error(__('Form doesn\'t exists any field', 'bit-integrations'));
        }

        $responseData['fields'] = $fields;
        $responseData['postId'] = $data->postId;
        wp_send_json_success($responseData);
    }

    public static function fields($data)
    {
        if (!isset($data->postId)) {
            return;
        }

        $post_meta      = get_post_meta($data->postId, 'brizy');
        $tamplate_form  = json_decode(base64_decode($post_meta[0]['brizy-post']['editor_data']));
        $formData       = self::get_tamplate_form_data_by_id($tamplate_form->items, $data->id);
        $formFields     = self::get_tamplate_form_data($formData);

        $fields = [];
        foreach ($formFields as $field) {
            $type = $field->field_type;
            if ($type === 'upload') {
                $type = 'file';
            }

            $fields[] = [
                'name'  => $field->field_id,
                'type'  => $type,
                'label' => $field->field_title,
            ];
        }
        return $fields;
    }

    public static function get_tamplate_form_id($items)
    {
        if (is_array($items)) {
            foreach ($items as $item) {
                if (isset($item->type) && $item->type !== "Form2") {
                    return self::get_tamplate_form_id($item->value->items);
                } else {
                    return $item->value->_id;
                }
            }
        } else {
            if (isset($items->type) && $items->type !== 'form2') {
                return self::get_tamplate_form_id($items->value->items);
            } else {
                return $items->value->_id;
            }
        }
    }

    public static function get_tamplate_form_data_by_id($items, $form_id)
    {
        if (is_array($items)) {
            foreach ($items as $item) {
                if (isset($item->type) && $item->type !== "Form2") {
                    return self::get_tamplate_form_data_by_id($item->value->items, $form_id);
                } else {
                    if ($item->value->_id == $form_id) {
                        return $item;
                    }
                }
            }
        } else {
            if (isset($items->type) && $items->type !== 'form2') {
                return self::get_tamplate_form_data_by_id($items->value->items, $form_id);
            } else {
                if ($items->value->_id == $form_id) {
                    return $items;
                }
            }
        }
    }

    public static function get_tamplate_form_data($items)
    {
        $field_data = [];
        if (is_array($items)) {
            foreach ($items as $item) {
                if (isset($item->value->items)) {
                    return self::get_tamplate_form_data($item->value->items);
                } else {
                    $field_data[] = (object) [
                        "field_id"      => $item->value->_id,
                        "field_type"    => strtolower($item->value->type),
                        "field_title"   => $item->value->label,
                    ];
                }
            }
        } else {
            if (isset($items->value->items)) {
                return self::get_tamplate_form_data($items->value->items);
            } else {
                $field_data[] = (object) [
                    "field_id"      => $items->value->_id,
                    "field_type"    => strtolower($items->value->type),
                    "field_title"   => $items->value->label,
                ];
            }
        }
        return $field_data;
    }

    private static function getBrizyPosts()
    {
        global $wpdb;

        $query = "SELECT ID, post_title, post_content, post_type FROM $wpdb->posts
        LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id)
        WHERE $wpdb->posts.post_status = 'publish' AND ($wpdb->posts.post_type = 'page' OR $wpdb->posts.post_type = 'post' OR $wpdb->posts.post_type = 'editor-template') AND $wpdb->postmeta.meta_key = 'brizy'";

        return $wpdb->get_results($query);
    }
}
