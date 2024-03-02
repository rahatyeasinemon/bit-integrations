<?php

/**
 * KirimEmail Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\KirimEmail;

use WP_Error;
use BitApps\BTCBI\Util\IpTool;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BitApps\BTCBI\Http\Services\Actions\KirimEmail\RecordApiHelper;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

/**
 * Provide functionality for KirimEmail integration
 */
class KirimEmailController
{
    // $time = time();
    // $generated_token = hash_hmac("sha256","YOUR USERNAME"."::"."YOUR API TOKEN"."::".$time,"YOUR API TOKEN")

    public function checkAuthorization($tokenRequestParams)
    {
        if (
            empty($tokenRequestParams->username)
            || empty($tokenRequestParams->api_key)
        ) {
            Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }
        $userName = $tokenRequestParams->username;
        $apiKey = $tokenRequestParams->api_key;
        $time = time();
        $generated_token = hash_hmac('sha256', "{$userName}" . '::' . "{$apiKey}" . '::' . $time, "{$apiKey}");
        $header = [
            'Auth-Id'    => $userName,
            'Auth-Token' => $generated_token,
            'Timestamp'  => $time,
        ];

        $apiEndpoint = 'https://api.kirim.email/v3/list';

        $apiResponse = Http::request($apiEndpoint, 'Get', null, $header);

        if (is_wp_error($apiResponse) || $apiResponse->code !== 200) {
            Response::error(
                empty($apiResponse->error) ? 'Unknown' : $apiResponse->error,
                400
            );
        }

        return Response::success($apiResponse->data);
    }

    public function getAllList($tokenRequestParams)
    {
        if (
            empty($tokenRequestParams->username)
            || empty($tokenRequestParams->api_key)
        ) {
            Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }
        $userName = $tokenRequestParams->username;
        $apiKey = $tokenRequestParams->api_key;
        $time = time();
        $generated_token = hash_hmac('sha256', "{$userName}" . '::' . "{$apiKey}" . '::' . $time, "{$apiKey}");
        $header = [
            'Auth-Id'    => $userName,
            'Auth-Token' => $generated_token,
            'Timestamp'  => $time,
        ];

        $apiEndpoint = 'https://api.kirim.email/v3/list';

        $apiResponse = Http::request($apiEndpoint, 'Get', null, $header);

        if (is_wp_error($apiResponse) || $apiResponse->code !== 200) {
            Response::error(
                empty($apiResponse->error) ? 'Unknown' : $apiResponse->error,
                400
            );
        }

        return Response::success($apiResponse->data);
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integrationId = $integrationData->id;
        $api_key = $integrationDetails->api_key;
        $userName = $integrationDetails->userName;
        $fieldMap = $integrationDetails->field_map;
        $mainAction = $integrationDetails->mainAction;

        if (
            empty($api_key) ||
            empty($integrationDetails)
            || empty($userName)
            || empty($fieldMap)

        ) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for Freshdesk api', 'bit-integrations'));
        }
        $recordApiHelper = new RecordApiHelper($integrationId);
        $kirinEmailApiResponse = $recordApiHelper->execute(
            $api_key,
            $userName,
            $fieldValues,
            $fieldMap,
            $integrationDetails,
            $mainAction
        );

        if (is_wp_error($kirinEmailApiResponse)) {
            return $kirinEmailApiResponse;
        }
        return $kirinEmailApiResponse;
    }
}
