<?php

/**
 * JetEngine Integration
 */

namespace BitCode\FI\Actions\JetEngine;

use WP_Error;
use Jet_Engine_CPT;
use Jet_Engine_Tools;

/**
 * Provide functionality for JetEngine integration
 */
class JetEngineController
{
    public function authentication()
    {
        if (self::checkedJetEngineExists()) {
            wp_send_json_success(true);
        } else {
            wp_send_json_error(
                __(
                    'Please! Install JetEngine',
                    'bit-integrations'
                ),
                400
            );
        }
    }

    public static function checkedJetEngineExists()
    {
        if (!is_plugin_active('jet-engine/jet-engine.php')) {
            wp_send_json_error(\sprintf(__('%s is not active or not installed', 'bit-integrations'), 'JetEngine Plugin'), 400);
        } else {
            return true;
        }
    }

    public function getMenuIcons()
    {
        self::checkedJetEngineExists();

        $JetEngineCPT = new Jet_Engine_CPT();
        $iconsOptions = $JetEngineCPT->get_icons_options();

        foreach ($iconsOptions as $icon) {
            $iconsOptionsList[] = (object) [
                'label' => $icon,
                'value' => 'dashicons-' . $icon
            ];
        }

        if (!empty($iconsOptionsList)) {
            wp_send_json_success($iconsOptionsList, 200);
        }

        wp_send_json_error(__('Icon options fetching failed!', 'bit-integrations'), 400);
    }

    public function getMenuPosition()
    {
        self::checkedJetEngineExists();

        $menuPositions = Jet_Engine_Tools::get_available_menu_positions();
        $menuPositionList = [];

        foreach ($menuPositions as $item) {
            $menuPositionList[] = (object) [
                'label' => $item['label'],
                'value' => (string) $item['value']
            ];
        }

        if (!empty($menuPositionList)) {
            wp_send_json_success($menuPositionList, 200);
        }

        wp_send_json_error(__('Menu position fetching failed!', 'bit-integrations'), 400);
    }

    public function getSupports()
    {
        self::checkedJetEngineExists();

        $JetEngineCPT = new Jet_Engine_CPT();
        $supportsOptions = $JetEngineCPT->get_supports_options();

        if (!empty($supportsOptions)) {
            wp_send_json_success($supportsOptions, 200);
        }

        wp_send_json_error(__('Support options fetching failed!', 'bit-integrations'), 400);
    }

    public function getTaxPostTypes()
    {
        self::checkedJetEngineExists();

        $postTypes = Jet_Engine_Tools::get_post_types_for_js();

        if (!empty($postTypes)) {
            wp_send_json_success($postTypes, 200);
        }

        wp_send_json_error(__('Post types fetching failed!', 'bit-integrations'), 400);
    }

    public function getRelationTypes()
    {
        self::checkedJetEngineExists();

        $types = jet_engine()->relations->types_helper->get_types_for_js();
        $typeList = [];

        if (!empty($types)) {
            foreach ($types as $type) {
                $typeList = array_merge($typeList, $type['options']);
            }
        }

        if (!empty($typeList)) {
            wp_send_json_success($typeList, 200);
        }

        wp_send_json_error(__('Relation types fetching failed!', 'bit-integrations'), 400);
    }

    public function execute($integrationData, $fieldValues)
    {
        self::checkedJetEngineExists();

        $integrationDetails = $integrationData->flow_details;
        $integId = $integrationData->id;
        $fieldMap = $integrationDetails->field_map;
        $selectedTask = $integrationDetails->selectedTask;
        $actions = (array) $integrationDetails->actions;

        if (empty($fieldMap) || empty($selectedTask)) {
            return new WP_Error('REQ_FIELD_EMPTY', __('Fields map, task are required for JetEngine', 'bit-integrations'));
        }

        $createCPTSelectedOptions = [
            'selectedMenuPosition' => $integrationDetails->selectedMenuPosition,
            'selectedMenuIcon'     => $integrationDetails->selectedMenuIcon,
            'selectedSupports'     => $integrationDetails->selectedSupports,
        ];

        $taxOptions = [
            'selectedTaxPostTypes' => $integrationDetails->selectedTaxPostTypes
        ];

        $relOptions = (array) $integrationDetails->relOptions;

        $recordApiHelper = new RecordApiHelper($integId);
        $jetEngineResponse = $recordApiHelper->execute($fieldValues, $fieldMap, $selectedTask, $actions, $createCPTSelectedOptions, $taxOptions, $relOptions);

        if (is_wp_error($jetEngineResponse)) {
            return $jetEngineResponse;
        }

        return $jetEngineResponse;
    }
}
