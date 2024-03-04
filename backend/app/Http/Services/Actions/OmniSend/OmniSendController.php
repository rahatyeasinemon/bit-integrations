<?php

/**
 * OmniSend Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\OmniSend;

use BitApps\BTCBI\Http\Services\Actions\OmniSend\RecordApiHelper;
use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

/**
 * Provide functionality for OmniSend integration
 */
class OmniSendController
{
    private $baseUrl = 'https://api.omnisend.com/v3/';
    protected $_defaultHeader;

    public function authorization($requestParams)
    {
        if (empty($requestParams->api_key)) {
            return Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }

        $apiEndpoint = $this->baseUrl . 'contacts';

        $header = [
            'X-API-KEY' => $requestParams->api_key,
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $header);
        if (isset($response->contacts)) {
            return Response::success('');
        } else {
            return Response::error(
                'The token is invalid',
                400
            );
        }
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId = $integrationData->id;
        $api_key = $integrationDetails->api_key;
        $channels = $integrationDetails->channels;
        $fieldMap = $integrationDetails->field_map;
        $emailStatus = $integrationDetails->email_status;
        $smsStatus = $integrationDetails->sms_status;

        if (
            empty($fieldMap)
             || empty($api_key)
        ) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for OmniSend api', 'bit-integrations'));
        }
        $recordApiHelper = new RecordApiHelper($integrationDetails, $integId);

        $omniSendApiResponse = $recordApiHelper->execute(
            $channels,
            $emailStatus,
            $smsStatus,
            $fieldValues,
            $fieldMap
        );

        if (is_wp_error($omniSendApiResponse)) {
            return $omniSendApiResponse;
        }
        return $omniSendApiResponse;
    }
}
