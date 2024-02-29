<?php

/**
 * ClinchPad Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\ClinchPad;

use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

/**
 * Provide functionality for ClinchPad integration
 */
class ClinchPadController
{
    protected $_defaultHeader;
    protected $apiEndpoint;

    public function __construct()
    {
        $this->apiEndpoint = "https://www.clinchpad.com/api/v1";
    }

    public function authentication($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->api_key)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiKey      = $fieldsRequestParams->api_key;
        $apiEndpoint = $this->apiEndpoint . "/users";
        $headers = [
            "Authorization" => 'Basic ' . base64_encode("api-key:$apiKey")
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $headers);

        if (isset($response)) {
            Response::success('Authentication successful');
        } else {
            Response::error('Please enter valid API key', 400);
        }
    }

    public function getAllParentOrganizations($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->api_key)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiKey      = $fieldsRequestParams->api_key;
        $apiEndpoint = $this->apiEndpoint . "/organizations";
        $headers = [
            "Authorization" => 'Basic ' . base64_encode("api-key:$apiKey")
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $headers);

        if (isset($response)) {
            foreach ($response as $parentOrganization) {
                $parentOrganizations[] = [
                    'id'   => (string) $parentOrganization->_id,
                    'name' => $parentOrganization->name
                ];
            }
            Response::success($parentOrganizations);
        } else {
            Response::error('ParentOrganizations fetching failed', 400);
        }
    }

    public function getAllCRMPipelines($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->api_key)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiKey      = $fieldsRequestParams->api_key;
        $apiEndpoint = $this->apiEndpoint . "/pipelines";
        $headers = [
            "Authorization" => 'Basic ' . base64_encode("api-key:$apiKey")
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $headers);

        if (!empty($response)) {
            foreach ($response as $pipeline) {
                $pipelines[] = [
                    'id'   => $pipeline->_id,
                    'name' => $pipeline->name
                ];
            }
            Response::success($pipelines);
        } else {
            Response::error('Pipelines fetching failed', 400);
        }
    }

    public function getAllCRMContacts($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->api_key)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiKey      = $fieldsRequestParams->api_key;
        $apiEndpoint = $this->apiEndpoint . "/contacts";
        $headers = [
            "Authorization" => 'Basic ' . base64_encode("api-key:$apiKey")
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $headers);

        if (!empty($response)) {
            foreach ($response as $contact) {
                $contacts[] = [
                    'id'   => $contact->_id,
                    'name' => $contact->name
                ];
            }
            Response::success($contacts);
        } else {
            Response::error('Contacts fetching failed', 400);
        }
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId            = $integrationData->id;
        $authToken          = $integrationDetails->api_key;
        $fieldMap           = $integrationDetails->field_map;
        $actionName         = $integrationDetails->actionName;

        if (empty($fieldMap) || empty($authToken) || empty($actionName)) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for ClinchPad api', 'bit-integrations'));
        }

        $recordApiHelper   = new RecordApiHelper($integrationDetails, $integId);
        $clinchPadApiResponse = $recordApiHelper->execute($fieldValues, $fieldMap, $actionName);

        if (is_wp_error($clinchPadApiResponse)) {
            return $clinchPadApiResponse;
        }
        return $clinchPadApiResponse;
    }
}
