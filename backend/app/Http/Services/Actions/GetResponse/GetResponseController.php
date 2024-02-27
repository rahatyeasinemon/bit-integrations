<?php

/**
 * GetResponse Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\GetResponse;

use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;

/**
 * Provide functionality for GetResponse integration
 */
class GetResponseController
{
    private $baseUrl = 'https://api.getresponse.com/v3/';
    protected $_defaultHeader;

    public function fetchCustomFields($requestParams)
    {
        if (empty($requestParams->auth_token)) {
            wp_send_json_error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }

        $apiEndpoint = $this->baseUrl . 'custom-fields';
        $apiKey       = $requestParams->auth_token;
        $header       = [
            'X-Auth-Token' => 'api-key ' . $apiKey,
        ];

        $response          = Http::request($apiEndpoint, 'Get', null, $header);
        $formattedResponse = [];

        foreach ($response as $value) {
            $formattedResponse[] =
                [
                    'key'      => $value->customFieldId,
                    'label'    => ucfirst(str_replace("_", " ", $value->name)),
                    'required' => false
                ];
        }

        if ($response !== 'Unauthorized') {
            wp_send_json_success($formattedResponse, 200);
        } else {
            wp_send_json_error(
                'The token is invalid',
                400
            );
        }
    }

    public function fetchAllTags($requestParams)
    {
        if (empty($requestParams->auth_token)) {
            wp_send_json_error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }

        $apiEndpoint = $this->baseUrl . 'tags';
        $apiKey       = $requestParams->auth_token;
        $header       = [
            'X-Auth-Token' => 'api-key ' . $apiKey,
        ];

        $response          = Http::request($apiEndpoint, 'Get', null, $header);
        $formattedResponse = [];

        foreach ($response as $value) {
            $formattedResponse[] =
                [
                    'tagId' => $value->tagId,
                    'name'  => $value->name,
                ];
        }

        if ($response !== 'Unauthorized') {
            wp_send_json_success($formattedResponse, 200);
        } else {
            wp_send_json_error(
                'The token is invalid',
                400
            );
        }
    }

    public function authentication($refreshFieldsRequestParams)
    {
        if (empty($refreshFieldsRequestParams->auth_token)) {
            wp_send_json_error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }
        $apiEndpoint = $this->baseUrl . 'campaigns';

        $apiKey = $refreshFieldsRequestParams->auth_token;

        $header = [
            'X-Auth-Token' => 'api-key ' . $apiKey,
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $header);

        $campaigns = [];

        foreach ($response as $campaign) {
            $campaigns[] = [
                'campaignId' => $campaign->campaignId,
                'name'       => $campaign->name
            ];
        }

        if (property_exists($response[0], 'campaignId')) {
            wp_send_json_success($campaigns, 200);
        } else {
            wp_send_json_error('Please enter valid API key', 400);
        }
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId            = $integrationData->id;
        $auth_token         = $integrationDetails->auth_token;
        $selectedTags       = $integrationDetails->selectedTags;
        $fieldMap           = $integrationDetails->field_map;
        $type               = $integrationDetails->mailer_lite_type;
        $campaignId         = $integrationDetails->campaignId;
        $campaign           = (object) ["campaignId" => $campaignId];

        if (
            empty($fieldMap)
            || empty($auth_token) || empty($campaignId)
        ) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for GetResponse api', 'bit-integrations'));
        }
        $recordApiHelper = new RecordApiHelper($integrationDetails, $integId);
        $getResponseApiResponse = $recordApiHelper->execute(
            $selectedTags,
            $type,
            $fieldValues,
            $fieldMap,
            $auth_token,
            $campaign
        );

        if (is_wp_error($getResponseApiResponse)) {
            return $getResponseApiResponse;
        }
        return $getResponseApiResponse;
    }
}
