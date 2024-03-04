<?php

namespace BitApps\BTCBI\Http\Services\Actions\Getgist;

use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;

use BitApps\BTCBI\Http\Services\Actions\Getgist\RecordApiHelper;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

class GetgistController
{
    public const APIENDPOINT = 'https://api.getgist.com';

    public static function getgistAuthorize($requestsParams)
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

        $apiEndpoint = self::APIENDPOINT . '/contacts';
        $authorizationHeader["Authorization"] = "Bearer {$requestsParams->api_key}";
        $apiResponse = Http::request($apiEndpoint, 'Get', null, $authorizationHeader);

        if (is_wp_error($apiResponse) || $apiResponse->code === 'authentication_failed') {
            return Response::error(
                empty($apiResponse->code) ? 'Unknown' : $apiResponse->message,
                400
            );
        }

        return Response::success(true);
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
        $getgistApiResponse = $recordApiHelper->execute(
            $integId,
            $fieldValues,
            $fieldMap,
            $integrationDetails
        );

        if (is_wp_error($getgistApiResponse)) {
            return $getgistApiResponse;
        }
        return $getgistApiResponse;
    }
}
