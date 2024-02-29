<?php

/**
 * MailChimp Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\Sendy;

use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BitApps\BTCBI\Http\Services\Log\LogHandler;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

/**
 * Provide functionality for MailChimp integration
 */
class SendyController
{
    private $_integrationID;

    // public function __construct($integrationID)
    // {
    //     $this->_integrationID = $integrationID;
    // }

    /**
     * Process ajax request for generate_token
     *
     * @param Object $requestsParams Params for generate token
     *
     * @return JSON zoho crm api response and status
     */
    public static function sendyAuthorize($requestsParams)
    {
        if (empty($requestsParams->api_key)) {
            Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }
        $apiKey = $requestsParams->api_key;
        $sendy_url = $requestsParams->sendy_url;
        $apiEndpoint = "{$sendy_url}/includes/helpers/integrations/zapier/triggers/dropdowns.php?api_key={$apiKey}&event=brands";
        $authorizationHeader['Accept'] = 'application/json';
        // $authorizationHeader["api-key"] = $requestsParams->api_key;
        $apiResponse = Http::request($apiEndpoint, 'Get', null, $authorizationHeader);
        if (is_wp_error($apiResponse) || $apiResponse->status === 'error' || !count($apiResponse)) {
            Response::error(
                empty($apiResponse->code) ? 'Unknown' : $apiResponse->message,
                400
            );
        }

        Response::success(true);
    }

    public function getAllBrands($queryParams)
    {
        if (
            empty($queryParams->api_key)
            || empty($queryParams->sendy_url)
        ) {
            Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }
        $apiKey = $queryParams->api_key;
        $sendy_url = $queryParams->sendy_url;
        $apiEndpoint = "{$sendy_url}/api/brands/get-brands.php";
        $authorizationHeader['Accept'] = 'application/json';
        $requestsParams = [
            'api_key' => $apiKey
        ];
        // $authorizationHeader["api-key"] = $queryParams->api_key;
        $apiResponse = Http::request($apiEndpoint, 'Post', $requestsParams, $authorizationHeader);
        $response = [];
        foreach ($apiResponse as $list) {
            $response[] = (object) [
                'brandId' => $list->id,
                'brandName' => $list->name
            ];
        }
        Response::success($response);
    }

    public function getAllLists($queryParams)
    {
        if (
            empty($queryParams->api_key)
            || empty($queryParams->sendy_url)
        ) {
            Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }
        $apiKey = $queryParams->api_key;
        $sendy_url = $queryParams->sendy_url;
        $brand_id = $queryParams->brand_id;
        $apiEndpoint = "{$sendy_url}/api/lists/get-lists.php";
        $authorizationHeader['Accept'] = 'application/json';
        // $authorizationHeader["api-key"] = $queryParams->api_key;
        $requestsParams = [
            'api_key' => $apiKey,
            'brand_id' => $brand_id
        ];
        $apiResponse = Http::request($apiEndpoint, 'Post', $requestsParams, $authorizationHeader);

        $response = [];
        foreach ($apiResponse as $list) {
            $response[] = (object) [
                'listId' => $list->id,
                'listName' => $list->name,
            ];
        }
        Response::success($response);
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $fieldMap = $integrationDetails->field_map;
        $apiKey = $integrationDetails->api_key;
        $integId = $integrationData->id;

        if (
            empty($apiKey)
            || empty($fieldMap)
        ) {
            $error = new WP_Error('REQ_FIELD_EMPTY', __('api key, fields map are required for sendy api', 'bit-integrations'));
            LogHandler::save($integId, 'contact', 'validation', $error);
            return $error;
        }
        $recordApiHelper = new RecordApiHelper($integId);
        $hubspotResponse = $recordApiHelper->execute(
            $integId,
            $integrationDetails,
            $fieldValues,
            $fieldMap,
            $apiKey
        );
        if (is_wp_error($hubspotResponse)) {
            return $hubspotResponse;
        }
        return $hubspotResponse;
    }
}
