<?php

/**
 * Smaily Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\Smaily;

use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

/**
 * Provide functionality for Smaily integration
 */
class SmailyController
{
    public function authentication($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->subdomain) && empty($fieldsRequestParams->api_user_name) && empty($fieldsRequestParams->api_user_password)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $subdomain       = $fieldsRequestParams->subdomain;
        $apiEndpoint     = "https://$subdomain.sendsmaily.net/api/organizations/users.php";
        $apiUserName     = $fieldsRequestParams->api_user_name;
        $apiUserPassword = $fieldsRequestParams->api_user_password;
        $header          = [
            'Authorization' => 'Basic ' . base64_encode("$apiUserName:$apiUserPassword")
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $header);

        if (isset($response[0]->id) && !empty($response)) {
            Response::success('authentication successful');
        } else {
            Response::error('Please enter valid subdomain name and api credentials', 400);
        }
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId            = $integrationData->id;
        $fieldMap           = $integrationDetails->field_map;

        if (empty($fieldMap) || empty($integrationDetails->subdomain) || empty($integrationDetails->api_user_name) || empty($integrationDetails->api_user_password)) {
            return new WP_Error('REQ_FIELD_EMPTY', __('fields are required for Smaily api', 'bit-integrations'));
        }

        $recordApiHelper   = new RecordApiHelper($integrationDetails, $integId);
        $smailyApiResponse = $recordApiHelper->execute($fieldValues, $fieldMap);

        if (is_wp_error($smailyApiResponse)) {
            return $smailyApiResponse;
        }
        return $smailyApiResponse;
    }
}
