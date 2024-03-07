<?php

/**
 * ZohoSheet Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\ElasticEmail;

use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;

use BitApps\BTCBI\Http\Services\Actions\ElasticEmail\RecordApiHelper;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

/**
 * Provide functionality for ZohoCrm integration
 */
class ElasticEmailController
{
    public static function elasticEmailAuthorize($requestsParams)
    {
        if (empty($requestsParams->api_key)) {
            return Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }

        $apiEndpoint = "https://api.elasticemail.com/v4/lists";
        $apiKey = $requestsParams->api_key;
        $header = [
            'X-ElasticEmail-ApiKey' => $apiKey,
            'Accept' => '*/*',
        ];
        $apiResponse = Http::request($apiEndpoint, 'Get', null, $header);
        if (is_wp_error($apiResponse) || !is_null($apiResponse->Error)) {
            return Response::error(
                empty($apiResponse->code) ? 'Unknown' : $apiResponse->Error,
                400
            );
        }
        return Response::success(true);
    }
    public static function getAllLists($requestsParams)
    {
        if (empty($requestsParams->apiKey)) {
            return Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }

        $apiEndpoint = "https://api.elasticemail.com/v4/lists";
        $apiKey = $requestsParams->apiKey;
        $header = [
            'X-ElasticEmail-ApiKey' => $apiKey,
            'Accept' => '*/*',
        ];
        $apiResponse = Http::request($apiEndpoint, 'Get', null, $header);
        $data = [];
        foreach ($apiResponse as $list) {
            $data[] = (object) [
                'listId' => $list->PublicListID,
                'listName' => $list->ListName
            ];
        }
        $response['lists'] = $data;
        return Response::success($response);
        // Response::success(true);
    }




    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId = $integrationData->id;

        $api_key = $integrationDetails->api_key;
        $fieldMap = $integrationDetails->field_map;
        $actions = $integrationDetails->actions;
        if (empty($api_key)
            || empty($fieldMap)
        ) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for Sendinblue api', 'bit-integrations'));
        }
        $recordApiHelper = new RecordApiHelper($api_key, $integId);
        $elasticEmailApiResponse = $recordApiHelper->execute(
            $integId,
            $fieldValues,
            $fieldMap,
            $integrationDetails
            // $actions
        );

        if (is_wp_error($elasticEmailApiResponse)) {
            return $elasticEmailApiResponse;
        }
        return $elasticEmailApiResponse;
    }
}
