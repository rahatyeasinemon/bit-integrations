<?php

/**
 * Rapidmail Integration
 *
 */

namespace BitApps\BTCBI\Http\Services\Actions\Twilio;

use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BitApps\BTCBI\Http\Services\Log\LogHandler;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

final class TwilioController
{
    private $_integrationID;
    public static $apiBaseUri = 'https://api.twilio.com/2010-04-01';
    protected $_defaultHeader;

    public function __construct($integrationID)
    {
        $this->_integrationID = $integrationID;
    }

    public static function checkAuthorization($tokenRequestParams)
    {
        if (
            empty($tokenRequestParams->sid)
            || empty($tokenRequestParams->token)
            || empty($tokenRequestParams->from_num)
        ) {
            Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }
        $header = [
            'Authorization' => 'Basic ' . base64_encode("$tokenRequestParams->sid:$tokenRequestParams->token"),
            'Accept' => '*/*',
            'verify' => false
        ];
        $apiEndpoint = self::$apiBaseUri . '/Accounts';

        $apiResponse = Http::request($apiEndpoint, 'Get', null, $header);

        $xml = simplexml_load_string($apiResponse);
        $json = json_encode($xml);
        $response = json_decode($json, true);

        if (array_key_exists('RestException', $response)) {
            Response::error(
                'Unauthorize',
                400
            );
        } else {
            Response::success($apiResponse);
        }
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $fieldMap = $integrationDetails->field_map;
        $sid = $integrationDetails->sid;
        $token = $integrationDetails->token;
        $from_num = $integrationDetails->from_num;

        if (
            empty($sid)
            || empty($token)
            || empty($from_num)
            || empty($fieldMap)
        ) {
            $error = new WP_Error('REQ_FIELD_EMPTY', __('SID, Auth Token,From Number and mapping fields are required for rapidmail api', 'bit-integrations'));
            LogHandler::save($this->_integrationID, 'twilio sms sending', 'validation', $error);
            return $error;
        }
        $recordApiHelper = new RecordApiHelper($integrationDetails, $sid, $token, $from_num);
        $twilioResponse = $recordApiHelper->executeRecordApi(
            $this->_integrationID,
            $fieldValues,
            $fieldMap,
            $integrationDetails
        );
        return $twilioResponse;
    }
}
