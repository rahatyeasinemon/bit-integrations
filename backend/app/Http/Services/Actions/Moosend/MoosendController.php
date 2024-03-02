<?php

/**
 * Moosend Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\Moosend;

use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BitApps\BTCBI\Http\Services\Actions\Moosend\RecordApiHelper;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

/**
 * Provide functionality for Moosend integration
 */

class MoosendController
{
    private $baseUrl = 'https://api.moosend.com/v3/';

    public function handleAuthorize($requestParams)
    {
        if (empty($requestParams->authKey)) {
            Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }
        $apiEndpoint = $this->baseUrl . 'lists/1/1000.json?apikey=' . $requestParams->authKey;
        $headers = [
          'Content-Type' => 'application/json',
          'Accept' => 'application/json',
        ];
        $response = Http::request($apiEndpoint, 'Get', null, $headers);
        if ($response->Error !== null) {
            Response::error(
                __(
                    'Invalid token',
                    'bit-integrations'
                ),
                400
            );
        }
        return Response::success($response);
    }


    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId = $integrationData->id;
        $authKey = $integrationDetails->authKey;
        $listId = $integrationDetails->listId;
        $method = $integrationDetails->method;
        $field_map = $integrationDetails->field_map;

        if (
            empty($field_map)
            || empty($authKey)
        ) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for moosend api', 'bit-integrations'));
        }
        $recordApiHelper = new RecordApiHelper($integrationDetails, $integId);
        $moosendApiResponse = $recordApiHelper->execute(
            $listId,
            $method,
            $fieldValues,
            $field_map,
            $authKey
        );

        if (is_wp_error($moosendApiResponse)) {
            return $moosendApiResponse;
        }
        return $moosendApiResponse;
    }
}
