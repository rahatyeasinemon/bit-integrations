<?php

/**
 * Klaviyo Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\Klaviyo;

use BitApps\BTCBI\Util\Common;
use BitApps\BTCBI\Util\Helper;
use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

/**
 * Provide functionality for Klaviyo integration
 */

class KlaviyoController
{
    private $baseUrl = 'https://a.klaviyo.com/api/v2/';

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
        $apiEndpoint = $this->baseUrl . 'lists?api_key=' . $requestParams->authKey;
        $response = Http::request($apiEndpoint, 'Get', null);
        if ($response->message === "The API key specified is invalid.") {
            Response::error(
                __(
                    'Invalid token',
                    'bit-integrations'
                ),
                400
            );
        }
        Response::success($response);
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId = $integrationData->id;
        $authKey = $integrationDetails->authKey;
        $listId = $integrationDetails->listId;
        $field_map = $integrationDetails->field_map;

        if (
            empty($field_map)
             || empty($authKey)
        ) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for Klaviyo api', 'bit-integrations'));
        }
        $recordApiHelper = new RecordApiHelper($integrationDetails, $integId);
        $klaviyoApiResponse = $recordApiHelper->execute(
            $listId,
            $fieldValues,
            $field_map,
            $authKey
        );

        if (is_wp_error($klaviyoApiResponse)) {
            return $klaviyoApiResponse;
        }
        return $klaviyoApiResponse;
    }
}
