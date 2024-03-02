<?php

/**
 * DirectIQ Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\DirectIq;

use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BitApps\BTCBI\Http\Services\Actions\DirectIq\RecordApiHelper;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

/**
 * Provide functionality for ZohoCrm integration
 */
class DirectIqController
{
    private $_integrationID;

    public function __construct($integrationID)
    {
        $this->_integrationID = $integrationID;
    }

    public static function _apiEndpoint($method)
    {
        return "https://clientapi.benchmarkemail.com/{$method}";
    }

    /**
     * Process ajax request
     *
     * @param $requestsParams Params to authorize
     *
     * @return JSON DirectIQ api response and status
     */
    public static function directIqAuthorize($requestsParams)
    {
        if (empty($requestsParams->client_id) || empty($requestsParams->client_secret)
        ) {
            Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }

        $curl = curl_init();

        $header = 'Basic ' . base64_encode("$requestsParams->client_id:$requestsParams->client_secret");

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://rest.directiq.com/subscription/authorize",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "authorization: {$header}"
            ],
        ]);

        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($statusCode !== 200) {
            Response::error(
                empty($apiResponse) ? 'Unknown' : $apiResponse,
                400
            );
        }

        Response::success(true);
    }

    /**
     * Process ajax request for refresh Lists
     *
     * @param $queryParams Params to fetch list
     *
     * @return JSON DirectIQ lists data
     */
    public static function directIqLists($queryParams)
    {
        if (empty($queryParams->client_id) || empty($queryParams->client_secret)
        ) {
            Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }

        $apiEndpoint = "https://rest.directiq.com/contacts/lists/list";

        $authorizationHeader['authorization'] = 'Basic ' . base64_encode("$queryParams->client_id:$queryParams->client_secret");
        $directIqResponse = Http::request($apiEndpoint, 'Get', null, $authorizationHeader);

        $lists = [];
        if (!is_wp_error($directIqResponse)) {
            $allLists = ($directIqResponse);

            foreach ($allLists as $key => $list) {
                $lists[$list->name] = (object) [
                    'listId' => $list->id,
                    'listName' => $list->name,
                ];
            }

            $response['directIqLists'] = $lists;
            return Response::success($response);
        }
    }

    /**
     * Process ajax request for refresh crm modules
     *
     * @param $queryParams Params to fetch headers
     *
     * @return JSON crm module data
     */
    public static function directIqHeaders($queryParams)
    {
        if (empty($queryParams->client_id) || empty($queryParams->client_secret)
        ) {
            Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }

        $listId = $queryParams->list_id;

        $apiEndpoint = "https://rest.directiq.com/subscription/fields";

        $authorizationHeader['authorization'] = 'Basic ' . base64_encode("$queryParams->client_id:$queryParams->client_secret");
        $directIqResponse = Http::request($apiEndpoint, 'Get', null, $authorizationHeader);


        $fields = [];
        if (!is_wp_error($directIqResponse)) {
            $allFields = $directIqResponse;

            foreach ($allFields as $field) {
                $fields[$field->shortCode] = (object) [
                    'fieldId' => $field->shortCode,
                    'fieldName' => $field->name,
                    'fieldValue' => strtolower(str_replace(' ', '_', $field->name)),
                    'required' =>  strtolower($field->name) == 'email' ? true : false
                ];
            }

            $response['directIqField'] = $fields;

            return Response::success($response);
        }
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;

        $client_id = $integrationDetails->client_id;
        $client_secret = $integrationDetails->client_secret;
        $fieldMap = $integrationDetails->field_map;
        $actions = $integrationDetails->actions;
        $listId = $integrationDetails->listId;

        if (empty($client_id) || empty($client_secret) || empty($fieldMap)
        ) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for DirectIQ api', 'bit-integrations'));
        }
        $recordApiHelper = new RecordApiHelper($client_id, $client_secret, $this->_integrationID);

        $directIqApiResponse = $recordApiHelper->execute(
            $fieldValues,
            $fieldMap,
            $actions,
            $listId,
        );

        if (is_wp_error($directIqApiResponse)) {
            return $directIqApiResponse;
        }
        return $directIqApiResponse;
    }
}
