<?php

/**
 * Klaviyo Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\Klaviyo;

use BitApps\BTCBI\Util\Common;
use BitApps\BTCBI\Util\Helper;
use WP_Error;
use BitApps\BTCBI\Util\HttpHelper;

/**
 * Provide functionality for Klaviyo integration
 */

class KlaviyoController
{
    private $baseUrl = 'https://a.klaviyo.com/api/v2/';

    public function handleAuthorize($requestParams)
    {
        if (empty($requestParams->authKey)) {
            wp_send_json_error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }
        $apiEndpoints = $this->baseUrl . 'lists?api_key=' . $requestParams->authKey;
        $response = HttpHelper::get($apiEndpoints, null);
        if ($response->message === "The API key specified is invalid.") {
            wp_send_json_error(
                __(
                    'Invalid token',
                    'bit-integrations'
                ),
                400
            );
        }
        wp_send_json_success($response, 200);
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
