<?php

namespace BitApps\BTCBI\Http\Services\Triggers\Met;

use BitApps\BTCBI\Model\Flow;
use BTCBI\Deps\BitApps\WPKit\Http\Request\Request;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

final class MetController
{
    public static function info()
    {
        $plugin_path = self::pluginActive('get_name');
        return [
            'name' => 'Met Form',
            'title' => 'Met Form - Flexible and Design-Friendly Contact Form builder plugin for WordPress',
            'slug' => $plugin_path,
            'pro' => 'metform/metform.php',
            'type' => 'form',
            'is_active' => self::pluginActive(),
            'activation_url' => wp_nonce_url(self_admin_url('plugins.php?action=activate&amp;plugin=' . $plugin_path . '&amp;plugin_status=all&amp;paged=1&amp;s'), 'activate-plugin_' . $plugin_path),
            'install_url' => wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $plugin_path), 'install-plugin_' . $plugin_path),
            'list' => [
                'action' => 'met/get',
                'method' => 'get',
            ],
            'fields' => [
                'action' => 'met/get/form',
                'method' => 'post',
                'data' => ['id']
            ],
        ];
    }

    public static function pluginActive($option = null)
    {
        if (is_plugin_active('metform-pro/metform-pro.php')) {
            return $option === 'get_name' ? 'metform-pro/metform-pro.php' : true;
        } elseif (is_plugin_active('metform/metform.php')) {
            return $option === 'get_name' ? 'metform/metform.php' : true;
        } else {
            return false;
        }
    }

    public static function get_all_forms()
    {
        $form_list = [];
        $args = [
            'posts_per_page' => -1,
            'post_type' => 'metform-form',
            'post_status' => 'publish',
        ];

        $forms = get_posts($args);
        return $forms;
    }

    public function getAll()
    {
        if (!self::pluginActive()) {
            return Response::error(__('Metform is not installed or activated', 'bit-integrations'));
        }

        $forms = self::get_all_forms();

        $all_forms = [];

        if ($forms) {
            foreach ($forms as $form) {
                $all_forms[] = (object) [
                    'id' => $form->ID,
                    'title' => $form->post_title,
                ];
            }
        }
        return Response::success($all_forms);
    }

    public function get_a_form(Request $data)
    {
        if (!self::pluginActive()) {
            return Response::error(__('Met Form is not installed or activated', 'bit-integrations'));
        }
        if (empty($data->id)) {
            return Response::error(__('Form doesn\'t exists', 'bit-integrations'));
        }

        $fields = self::fields($data->id);

        if (empty($fields)) {
            return Response::error(__('Form doesn\'t exists any field', 'bit-integrations'));
        }

        $responseData['fields'] = $fields;
        return Response::success($responseData);
    }

    public static function fields($form_id)
    {
        $input_widgets = \Metform\Widgets\Manifest::instance()->get_input_widgets();

        $widget_input_data = get_post_meta($form_id, '_elementor_data', true);
        $widget_input_data = json_decode($widget_input_data);
        $fieldDetails = \MetForm\Core\Entries\Map_El::data($widget_input_data, $input_widgets)->get_el();

        $fields = [];
        foreach ($fieldDetails as $key => $field) {
            $widgetType = $field->widgetType;
            $type = substr($widgetType, 3);
            $withoutText = ['radio', 'checkbox', 'select', 'date', 'time', 'attachment', 'email', 'poll', 'signature', 'file', 'file-upload', 'multi-select'];
            if ($type == 'file-upload') {
                $type = 'file';
            } elseif (!in_array($type, $withoutText)) {
                $type = 'text';
            }
            $fields[] = [
                'name' => $key,
                'type' => $type,
                'label' => $field->mf_input_label,
            ];
        }
        return $fields;
    }

    public static function handle_metform_pro_submit($form_setting, $form_data, $email_name)
    {
        self::handle_submit_data($form_data['id'], $form_data);
    }

    public static function handle_metform_submit($form_id, $form_data, $form_settings)
    {
        self::handle_submit_data($form_id, $form_data);
    }

    private static function handle_submit_data($form_id, $form_data)
    {
        if (!$form_id) {
            return;
        }
        $flows = Flow::exists('Met', $form_id);
        if (!$flows) {
            return;
        }

        unset($form_data['action'], $form_data['form_nonce'], $form_data['id']);
        $data = $form_data;
        Flow::execute('Met', $form_id, $data, $flows);
    }
}
