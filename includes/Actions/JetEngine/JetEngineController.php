<?php

/**
 * JetEngine Integration
 */

namespace BitCode\FI\Actions\JetEngine;

use WeDevs\JetEnginePro\Modules\Germanized\Helper;
use WP_Error;

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
            wp_send_json_error(__('JetEngine Plugin is not active or not installed', 'bit-integrations'), 400);
        } else {
            return true;
        }
    }

    public function getAllVendors()
    {
        self::checkedJetEngineExists();

        $allVendors = jetEngine()->vendor->get_vendors(['number' => -1]);
        $vendorList = [];

        foreach ($allVendors as $vendor) {
            $dispalyName = $vendor->data->display_name;
            $vendorArray = $vendor->to_array();

            $vendorList[] = (object) [
                'label' => $dispalyName . ' (' . $vendorArray['store_name'] . ')',
                'value' => (string) $vendorArray['id']
            ];
        }

        wp_send_json_success($vendorList, 200);
    }

    public function getEUFields()
    {
        self::checkedJetEngineExists();

        $fields = [];
        $vendorEUFields = [
            ['key' => 'jetEngine_company_name', 'label' => 'Company Name', 'required' => false],
            ['key' => 'jetEngine_company_id_number', 'label' => 'Company ID/EUID Number', 'required' => false],
            ['key' => 'jetEngine_vat_number', 'label' => 'VAT/TAX Number', 'required' => false],
            ['key' => 'jetEngine_bank_name', 'label' => 'Name of Bank', 'required' => false],
            ['key' => 'jetEngine_bank_iban', 'label' => 'Bank IBAN', 'required' => false],
        ];

        if (is_plugin_active('jet-engine/jet-engine.php') && jetEngine_pro()->module->is_active('germanized')) {
            $enabledEUFields = Helper::is_fields_enabled_for_seller();

            foreach ($enabledEUFields as $key => $item) {
                if ($item) {
                    $vendorEnabledEUFieldsKey[] = $key;
                }
            }

            if (!empty($vendorEnabledEUFieldsKey)) {
                foreach ($vendorEUFields as $vendorEUField) {
                    if (\in_array($vendorEUField['key'], $vendorEnabledEUFieldsKey)) {
                        $fields[] = (object) $vendorEUField;
                    }
                }
            }

            wp_send_json_success($fields, 200);
        }

        wp_send_json_error('EU Compliance Fields fetching failed - JetEngine Pro or EU Compliance Fields is not enabled', 400);
    }

    public function execute($integrationData, $fieldValues)
    {
        self::checkedJetEngineExists();

        $integrationDetails = $integrationData->flow_details;
        $integId = $integrationData->id;
        $fieldMap = $integrationDetails->field_map;
        $selectedTask = $integrationDetails->selectedTask;
        $selectedVendor = $integrationDetails->selectedVendor;
        $actions = (array) $integrationDetails->actions;
        $selectedPaymentMethod = $integrationDetails->selectedPaymentMethod;

        if (empty($fieldMap) || empty($selectedTask)) {
            return new WP_Error('REQ_FIELD_EMPTY', __('Fields map, task are required for JetEngine', 'bit-integrations'));
        }

        $recordApiHelper = new RecordApiHelper($integId);
        $jetEngineResponse = $recordApiHelper->execute($fieldValues, $fieldMap, $selectedTask, $actions, $selectedVendor, $selectedPaymentMethod);

        if (is_wp_error($jetEngineResponse)) {
            return $jetEngineResponse;
        }

        return $jetEngineResponse;
    }
}
