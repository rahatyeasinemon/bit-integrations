<?php

/**
 * Salesflare Integration
 */

namespace BitCode\FI\Actions\Salesflare;

use WP_Error;
use BitCode\FI\Core\Util\HttpHelper;

/**
 * Provide functionality for Salesflare integration
 */
class SalesflareController
{
    protected $_defaultHeader;
    protected $apiEndpoint;
    protected $domain;

    private function setApiEndpoint()
    {
        return $this->apiEndpoint = "https://api.salesflare.com";
    }

    private function checkValidation($fieldsRequestParams, $customParam = '**')
    {
        if (empty($fieldsRequestParams->api_key) || empty($customParam)) {
            wp_send_json_error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }
    }

    private function setHeaders($apiKey)
    {
        return
            [
                "Authorization" => "Bearer {$apiKey}",
                "Content-type"  => "application/json",
            ];
    }

    public function authentication($fieldsRequestParams)
    {
        $this->checkValidation($fieldsRequestParams);
        $apiKey         = $fieldsRequestParams->api_key;
        $apiEndpoint    = $this->setApiEndpoint() . "/accounts";
        $headers        = $this->setHeaders($apiKey);
        $response       = HttpHelper::get($apiEndpoint, null, $headers);

        if (!isset($response->error)) {
            wp_send_json_success('Authentication successful', 200);
        } else {
            wp_send_json_error('Please enter valid API Key', 400);
        }
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId            = $integrationData->id;
        $apiKey             = $integrationDetails->api_key;
        $apiSecret          = $integrationDetails->api_secret;
        $fieldMap           = $integrationDetails->field_map;
        $actionName         = $integrationDetails->actionName;
        $domain             = $integrationDetails->domain;

        if (empty($fieldMap) || empty($apiKey) || empty($apiSecret) || empty($actionName) || empty($domain)) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for Salesflare api', 'bit-integrations'));
        }

        $recordApiHelper        = new RecordApiHelper($integrationDetails, $integId, $apiKey, $apiSecret,  $domain);
        $salesflareApiResponse  = $recordApiHelper->execute($fieldValues, $fieldMap, $actionName);

        if (is_wp_error($salesflareApiResponse)) {
            return $salesflareApiResponse;
        }
        return $salesflareApiResponse;
    }
}
