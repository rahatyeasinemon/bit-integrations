<?php

namespace BitCode\FI\Triggers\Elementor;

use WP_Error;
use ReflectionClass;
use BitCode\FI\Flow\Flow;
use BitCode\FI\Core\Util\Helper;
use BitCode\FI\Triggers\TriggerController;

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

    public function getTestData()
    {
        return TriggerController::getTestData('elementor');
    }

    public function removeTestData($data)
    {
        return TriggerController::removeTestData($data, 'elementor');
    }

    private static function parseFlowDetails($flowDetails)
    {
        return is_string($flowDetails) ? json_decode($flowDetails) : $flowDetails;
    }

    public static function handle_elementor_submit($record)
    {
        $recordData     = ElementorHelper::extractRecordData($record);
        $formData       = ElementorHelper::setFields($recordData);
        $reOrganizeId   = $recordData['id'] . $recordData['form_post_id'];

        if (get_option('btcbi_elementor_test') !== false) {
            update_option('btcbi_elementor_test', [
                'formData'      => $formData,
                'primaryKey'    => [(object) ['key'   => 'id', 'value' => $recordData['id']]]
            ]);
        }

        $flows = ElementorHelper::fetchFlows($recordData['id'], $reOrganizeId);
        if (!$flows) {
            return;
        }

        foreach ($flows as $flow) {
            $flowDetails = static::parseFlowDetails($flow->flow_details);

            if (!isset($flowDetails->primaryKey) && ($flow->triggered_entity_id == $recordData['id'] || $flow->triggered_entity_id == $reOrganizeId)) {
                $data = ElementorHelper::prepareDataForFlow($record);
                Flow::execute('Elementor', $flow->triggered_entity_id, $data, array($flow));
                continue;
            }

            if (is_array($flowDetails->primaryKey) && ElementorHelper::isPrimaryKeysMatch($recordData, $flowDetails)) {
                $data = array_column($formData, 'value', 'name');
                Flow::execute('Elementor', $flow->triggered_entity_id, $data, array($flow));
            }
        }

        return ['type' => 'success'];
    }
}
