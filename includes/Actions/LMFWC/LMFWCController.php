<?php

/**
 * LMFWC Integration
 */

namespace BitCode\FI\Actions\LMFWC;

use WP_Error;
use BitCode\FI\Core\Util\HttpHelper;

/**
 * Provide functionality for LMFWC integration
 */
class LMFWCController
{
    protected $_defaultHeader;

    public function authentication($fieldsRequestParams)
    {
        $this->checkValidation($fieldsRequestParams);
        $this->setHeaders($fieldsRequestParams->api_key, $fieldsRequestParams->api_secret);

        $apiEndpoint = $fieldsRequestParams->base_url . '/wp-json/lmfwc/v2/licenses';
        $response = HttpHelper::get($apiEndpoint, null, $this->_defaultHeader, ['sslverify' => false]);

        if (is_wp_error($response)) {
            wp_send_json_error($response->get_error_message(), HttpHelper::$responseCode);
        }
        if ((isset($response->code) && $response->code === 'lmfwc_rest_data_error') || (isset($response->success) && $response->success)) {
            wp_send_json_success(__('Authentication successful', 'bit-integrations'), 200);
        }

        wp_send_json_error(!empty($response->message) ? $response->message : __('Please enter valid Consumer key & Consumer secret', 'bit-integrations'), 400);
    }

    public function getAllCustomers($fieldsRequestParams)
    {
        $this->checkValidation($fieldsRequestParams);
        $this->setHeaders($fieldsRequestParams->api_key, $fieldsRequestParams->api_secret);

        $customers = get_users([
            'role'   => 'customer',
            'number' => -1,
        ]);

        wp_send_json_success(array_map(function ($customer) {
            return ['id' => $customer->ID, 'name' => $customer->display_name];
        }, $customers), 200);
    }

    public function getAllSessions($fieldsRequestParams)
    {
        $this->checkValidation($fieldsRequestParams);
        $this->setHeaders($fieldsRequestParams->api_key, $fieldsRequestParams->api_secret);
        $apiEndpoint = $this->_apiEndpoint . "/event/{$fieldsRequestParams->event_id}";
        $response = HttpHelper::get($apiEndpoint, null, $this->_defaultHeader);

        if (!isset($response->errors)) {
            $sessions = [];
            foreach ($response->dates as $session) {
                $sessions[]
                = (object) [
                    'date_id'  => $session->date_id,
                    'datetime' => $session->datetime
                ]
                ;
            }
            wp_send_json_success($sessions, 200);
        } else {
            wp_send_json_error(__('Events fetching failed', 'bit-integrations'), 400);
        }
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId = $integrationData->id;
        $apiKey = $integrationDetails->api_key;
        $apiSecret = $integrationDetails->api_secret;
        $baseUrl = $integrationDetails->base_url;
        $fieldMap = $integrationDetails->field_map;
        $module = $integrationDetails->module;

        if (empty($fieldMap) || empty($apiSecret) || empty($module) || empty($apiKey) || empty($baseUrl)) {
            return new WP_Error('REQ_FIELD_EMPTY', wp_sprintf(__('module, fields are required for %s api', 'bit-integrations'), 'License Manager For WooCommerce'));
        }

        $recordApiHelper = new RecordApiHelper($integrationDetails, $integId, $apiSecret, $apiKey, $baseUrl);
        $lmfwcApiResponse = $recordApiHelper->execute($fieldValues, $fieldMap, $module);

        if (is_wp_error($lmfwcApiResponse)) {
            return $lmfwcApiResponse;
        }

        return $lmfwcApiResponse;
    }

    private function checkValidation($fieldsRequestParams, $customParam = '**')
    {
        if (empty($fieldsRequestParams->base_url) || empty($fieldsRequestParams->api_key) || empty($fieldsRequestParams->api_secret) || empty($customParam)) {
            wp_send_json_error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }
    }

    private function setHeaders($apiKey, $apiSecret)
    {
        $this->_defaultHeader = [
            'Authorization' => 'Basic ' . base64_encode("{$apiKey}:{$apiSecret}"),
            'Content-Type'  => 'application/json',
        ];
    }
}
