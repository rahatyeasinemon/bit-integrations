<?php

/**
 * Woodpecker Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\Woodpecker;

use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

/**
 * Provide functionality for Woodpecker integration
 */
class WoodpeckerController
{
    protected $_defaultHeader;
    protected $apiEndpoint;
    protected $domain;

    private function setApiEndpoint()
    {
        return $this->apiEndpoint = "https://api.woodpecker.co/rest/v1";
    }

    private function checkValidation($fieldsRequestParams, $customParam = '**')
    {
        if (empty($fieldsRequestParams->api_key) || empty($customParam)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }
    }

    private function setHeaders($apiKey)
    {
        return
            [
                "Authorization" => "Basic $apiKey",
                "Content-type"  => "application/json",
            ];
    }

    public function authentication($fieldsRequestParams)
    {
        $this->checkValidation($fieldsRequestParams);
        $apiKey         = $fieldsRequestParams->api_key;
        $apiEndpoint    = $this->setApiEndpoint() . "/campaign_list";
        $headers        = $this->setHeaders(base64_encode($apiKey));
        $response       = Http::request($apiEndpoint, 'Get', null, $headers);

        if (isset($response->status) && $response->status->status === "ERROR") {
            Response::error('Please enter valid API Key', 400);
        } else {
            Response::success('Authentication successful');
        }
    }

    public function getAllCampagns($fieldsRequestParams)
    {
        $this->checkValidation($fieldsRequestParams);
        $apiKey         = $fieldsRequestParams->api_key;
        $apiEndpoint    = $this->setApiEndpoint() . "/campaign_list";
        $headers        = $this->setHeaders(base64_encode($apiKey));
        $response       = Http::request($apiEndpoint, 'Get', null, $headers);

        if (isset($response->status) && $response->status->status === "ERROR") {
            Response::error('Campaign not found!', 400);
        } else {
            $campaigns = [];
            foreach ($response as $campaign) {
                array_push(
                    $campaigns,
                    (object) [
                        'id' => $campaign->id,
                        'name' => $campaign->name,
                    ]
                );
            }

            Response::success($campaigns);
        }
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId            = $integrationData->id;
        $apiKey             = $integrationDetails->api_key;
        $actions            = $integrationDetails->actions;
        $fieldMap           = $integrationDetails->field_map;
        $actionName         = $integrationDetails->actionName;

        if (empty($fieldMap) || empty($apiKey) || empty($actionName)) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for Woodpecker api', 'bit-integrations'));
        }

        $recordApiHelper        = new RecordApiHelper($integrationDetails, $integId, base64_encode($apiKey));
        $woodpeckerApiResponse  = $recordApiHelper->execute($fieldValues, $fieldMap, $actionName, $actions);

        if (is_wp_error($woodpeckerApiResponse)) {
            return $woodpeckerApiResponse;
        }
        return $woodpeckerApiResponse;
    }
}
