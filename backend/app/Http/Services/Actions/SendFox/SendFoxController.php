<?php

/**
 * SendFox Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\SendFox;

use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

class SendFoxController
{
    private $baseUrl = 'https://api.sendfox.com/';

    public function sendFoxAuthorize($requestParams)
    {
        if (empty($requestParams->access_token)) {
            Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }
        $apiEndpoint = $this->baseUrl . 'me';

        $requestParams = [
            'Authorization' => "Bearer {$requestParams->access_token}",
            'Accept' => 'application/json',
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $requestParams);
        if ($response->message !== 'Unauthenticated.') {
            Response::success($response);
        } else {
            Response::error(
                'The token is invalid',
                400
            );
        }
    }

    public function fetchContactLists($requestParams)
    {
        if (empty($requestParams->access_token)) {
            Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }
        $apiEndpoint = $this->baseUrl . 'lists?page=1&limit=1000';

        $requestParams = [
            'Authorization' => "Bearer {$requestParams->access_token}",
            'Accept' => 'application/json',
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $requestParams);

        if ($response->message !== 'Unauthenticated.') {
            Response::success($response);
        } else {
            Response::error(
                'The token is invalid',
                400
            );
        }
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId = $integrationData->id;
        $access_token = $integrationDetails->access_token;
        $listId = $integrationDetails->listId;
        $fieldMap = $integrationDetails->field_map;

        if (
            // empty($listId)||
            // empty($fieldMap)||
            empty($access_token)
        ) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for SendFox api', 'bit-integrations'));
        }
        $recordApiHelper = new RecordApiHelper($integrationDetails, $integId);
        $acumbamailApiResponse = $recordApiHelper->execute(
            $listId,
            $fieldValues,
            $fieldMap,
            $access_token,
            $integrationDetails
        );

        if (is_wp_error($acumbamailApiResponse)) {
            return $acumbamailApiResponse;
        }
        return $acumbamailApiResponse;
    }
}
