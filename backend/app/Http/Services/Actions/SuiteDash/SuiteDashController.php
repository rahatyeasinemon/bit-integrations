<?php

/**
 * SuiteDash Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\SuiteDash;

use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

/**
 * Provide functionality for SuiteDash integration
 */
class SuiteDashController
{
    protected $_defaultHeader;
    protected $_apiEndpoint;

    public function __construct()
    {
        $this->_apiEndpoint = "https://app.suitedash.com/secure-api";
    }

    private function checkValidation($fieldsRequestParams, $customParam = '**')
    {
        if (empty($fieldsRequestParams->public_id) || empty($fieldsRequestParams->secret_key) || empty($customParam)) {
            return Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }
    }

    private function setHeaders($publicId, $secretKey)
    {
        $this->_defaultHeader = [
            "accept"       => "application/json",
            "X-Public-ID"  => $publicId,
            "X-Secret-Key" => $secretKey
        ];
    }

    public function authentication($fieldsRequestParams)
    {
        $this->checkValidation($fieldsRequestParams);
        $this->setHeaders($fieldsRequestParams->public_id, $fieldsRequestParams->secret_key);
        $apiEndpoint  = $this->_apiEndpoint . "/contacts";
        $response     = Http::request($apiEndpoint, 'Get', null, $this->_defaultHeader);

        if (isset($response->success) && $response->success) {
            return Response::success('Authentication successful');
        } else {
            return Response::error('Please enter valid Session Token or Link Name', 400);
        }
    }

    public function getAllFields($fieldsRequestParams)
    {
        $this->checkValidation($fieldsRequestParams);
        $this->setHeaders($fieldsRequestParams->public_id, $fieldsRequestParams->secret_key);
        $apiEndpoint  = $this->_apiEndpoint . "/contact/meta";
        $response = Http::request($apiEndpoint, 'Get', null, $this->_defaultHeader);

        if (isset($response->success) && $response->success === false) {
            return Response::error('Fields fetching failed', 400);
        } else {
            $fieldMap = [];
            $fieldNames   = ['uid', 'name_prefix', 'active', 'role', 'tags', 'created', 'company', 'companies'];
            foreach ($response as $key => $field) {
                if (array_search($key, $fieldNames) === false && $key !== 'custom_fields' && $key !== 'address') {
                    array_push(
                        $fieldMap,
                        [
                            'key' => $key,
                            'label' => $field->field_name,
                            'required' => $field->required
                        ]
                    );
                } elseif (array_search($key, $fieldNames) === false && $key === 'address') {
                    foreach ($field->properties as $addressFKey => $addressField) {
                        array_push(
                            $fieldMap,
                            [
                                'key' => "address-{$addressFKey}",
                                'label' => $addressField->field_name,
                                'required' => $addressField->required
                            ]
                        );
                    }
                } elseif (array_search($key, $fieldNames) === false && $key === 'custom_fields') {
                    foreach ($field->properties as $customFKey => $customField) {
                        array_push(
                            $fieldMap,
                            [
                                'key' => "custom-{$customFKey}",
                                'label' => $customField->field_name,
                                'required' => $customField->required
                            ]
                        );
                    }
                }
            }

            return Response::success($fieldMap);
        }
    }

    public function getAllCompanies($fieldsRequestParams)
    {
        $this->checkValidation($fieldsRequestParams);
        $this->setHeaders($fieldsRequestParams->public_id, $fieldsRequestParams->secret_key);
        $apiEndpoint  = $this->_apiEndpoint . "/companies";
        $response     = Http::request($apiEndpoint, 'Get', null, $this->_defaultHeader);

        if (isset($response->success) && $response->success) {
            $companies = [];
            foreach ($response->data as $company) {
                array_push(
                    $companies,
                    $company->name
                );
            }
            return Response::success($companies);
        } else {
            return Response::error('Tags fetching failed', 400);
        }
    }


    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId            = $integrationData->id;
        $publicId           = $integrationDetails->public_id;
        $secretKey          = $integrationDetails->secret_key;
        $fieldMap           = $integrationDetails->field_map;
        $actionName         = $integrationDetails->actionName;

        if (empty($fieldMap) || empty($publicId) || empty($actionName) || empty($secretKey)) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for SuiteDash api', 'bit-integrations'));
        }

        $recordApiHelper      = new RecordApiHelper($integrationDetails, $integId, $publicId, $secretKey);
        $suiteDashApiResponse = $recordApiHelper->execute($fieldValues, $fieldMap, $actionName);

        if (is_wp_error($suiteDashApiResponse)) {
            return $suiteDashApiResponse;
        }
        return $suiteDashApiResponse;
    }
}
