<?php

/**
 * Drip Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\Drip;

use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BitApps\BTCBI\Http\Services\Actions\Drip\RecordApiHelper;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

/**
 * Provide functionality for ZohoCrm integration
 */
class DripController
{
    private $_integrationID;

    public function __construct($integrationID)
    {
        $this->_integrationID = $integrationID;
    }

    /**
     * Process ajax request
     *
     * @param $requestsParams Params to authorize
     *
     * @return JSON Drip api response and status
     */
    public static function dripAuthorize($requestsParams)
    {
        if (empty($requestsParams->api_token)
        ) {
            return Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }

        $header['Authorization'] = 'Basic ' . base64_encode("$requestsParams->api_token:");

        $apiEndpoint = "https://api.getdrip.com/v2/accounts";

        $response = Http::request($apiEndpoint, 'Get', null, $header);

        if (!isset($response->accounts)) {
            return Response::error(
                empty($apiResponse) ? 'Unknown' : $apiResponse,
                400
            );
        }

        return Response::success($response);
    }

    /**
     * Process ajax request for refresh Campaigns
     *
     * @param $queryParams Params to fetch campaign
     *
     * @return JSON Drip campaigns data
     */
    public static function dripCampaigns($queryParams)
    {
        if (empty($queryParams->api_token)
        ) {
            return Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }
        $accountId = $queryParams->account_id;
        $header['Authorization'] = 'Basic ' . base64_encode("$queryParams->api_token:");

        $apiEndpoint = "https://api.getdrip.com/v2/{$accountId}/broadcasts";

        $dripResponse = Http::request($apiEndpoint, 'Get', null, $header);

        $campaigns = [];
        if (!is_wp_error($dripResponse->broadcasts)) {
            $allCampaigns = ($dripResponse->broadcasts);

            foreach ($allCampaigns as $key => $broadcast) {
                $campaigns[$broadcast->name] = (object) [
                    'campaignId' => $broadcast->id,
                    'campaignName' => $broadcast->name,
                ];
            }

            $response['dripCampaigns'] = $campaigns;
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
    public static function dripHeaders($queryParams)
    {
        if (empty($queryParams->api_token)
        ) {
            return Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }

        $campaignId = $queryParams->campaign_id;

        $apiEndpoint = "https://rest.directiq.com/subscription/fields";

        $authorizationHeader['authorization'] = 'Basic ' . base64_encode(":$queryParams->api_token");

        $dripResponse = Http::request($apiEndpoint, 'Get', null, $authorizationHeader);


        $customFields = [];
        if (!is_wp_error($dripResponse)) {
            $allFields = $dripResponse;

            foreach ($allFields as $field) {
                $customFields[] = (object) [
                    'fieldName' => $field->name,
                    'fieldValue' => strtolower(str_replace(' ', '_', $field->name)),
                ];
            }

            $defaultFields = array(
                (object) array('fieldValue' => 'email', 'fieldName' => 'Email', 'required' => true),
                (object) array('fieldValue' => 'first_name', 'fieldName' => 'First Name', 'required' => false),
                (object) array('fieldValue' => 'last_name', 'fieldName' => 'Last Name', 'required' => false),
            );

            $fields = array_merge($defaultFields, $customFields);

            $response['dripField'] = $fields;

            return Response::success($response);
        }
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;

        $api_token = $integrationDetails->api_token;
        $fieldMap = $integrationDetails->field_map;
        $actions = $integrationDetails->actions;
        $campaignId = $integrationDetails->campaignId;
        $account_id = $integrationDetails->account_id;

        if (empty($api_token) || empty($fieldMap)
        ) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for Drip api', 'bit-integrations'));
        }
        $recordApiHelper = new RecordApiHelper($api_token, $this->_integrationID);

        $dripApiResponse = $recordApiHelper->execute(
            $fieldValues,
            $fieldMap,
            $actions,
            $campaignId,
            $account_id
        );

        if (is_wp_error($dripApiResponse)) {
            return $dripApiResponse;
        }
        return $dripApiResponse;
    }
}
