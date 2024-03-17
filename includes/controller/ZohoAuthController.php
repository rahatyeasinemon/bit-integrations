<?php

namespace BitCode\FI\controller;

use BitCode\FI\Flow\FlowController;
use BitCode\FI\Core\Util\HttpHelper;

final class ZohoAuthController
{
    /**
     * Process ajax request for generate_token
     *
     * @param $requestsParams Mandatory params for generate tokens
     *
     * @return JSON zoho crm api response and status
     */
    public static function generateTokens($requestsParams)
    {
        if (
            empty($requestsParams->{'accounts-server'})
            || empty($requestsParams->dataCenter)
            || empty($requestsParams->clientId)
            || empty($requestsParams->clientSecret)
            || empty($requestsParams->redirectURI)
            || empty($requestsParams->code)
        ) {
            wp_send_json_error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }
        $apiEndpoint = \urldecode($requestsParams->{'accounts-server'}) . '/oauth/v2/token';
        $requestParams = array(
            "grant_type" => "authorization_code",
            "client_id" => $requestsParams->clientId,
            "client_secret" => $requestsParams->clientSecret,
            "redirect_uri" => \urldecode($requestsParams->redirectURI),
            "code" => $requestsParams->code
        );
        $apiResponse = HttpHelper::post($apiEndpoint, $requestParams);
        if (is_wp_error($apiResponse) || !empty($apiResponse->error)) {
            wp_send_json_error(
                empty($apiResponse->error) ? 'Unknown' : $apiResponse->error,
                400
            );
        }
        $apiResponse->generates_on = \time();
        wp_send_json_success($apiResponse, 200);
    }

    /**
     * Helps to refresh zoho crm access_token
     *
     * @param Object $apiData Contains required data for refresh access token
     *
     * @return JSON  $tokenDetails API token details
     */
    public static function _refreshAccessToken($apiData)
    {
        if (
            empty($apiData->dataCenter)
            || empty($apiData->clientId)
            || empty($apiData->clientSecret)
            || empty($apiData->tokenDetails)
        ) {
            return false;
        }
        $tokenDetails = $apiData->tokenDetails;

        $dataCenter = $apiData->dataCenter;
        $apiEndpoint = "https://accounts.{$dataCenter}/oauth/v2/token";
        $requestParams = array(
            "grant_type" => "refresh_token",
            "client_id" => $apiData->clientId,
            "client_secret" => $apiData->clientSecret,
            "refresh_token" => $tokenDetails->refresh_token,
        );

        $apiResponse = HttpHelper::post($apiEndpoint, $requestParams);
        if (is_wp_error($apiResponse) || !empty($apiResponse->error)) {
            return false;
        }
        $tokenDetails->generates_on = \time();
        $tokenDetails->access_token = $apiResponse->access_token;
        return $tokenDetails;
    }

    /**
     * Save updated access_token to avoid unnecessary token generation
     *
     * @param Integer $integrationID ID of Zoho crm Integration
     * @param Object  $tokenDetails  refreshed token info
     *
     * @return null
     */
    public static function _saveRefreshedToken($integrationID, $tokenDetails, $others = null)
    {
        if (empty($integrationID)) {
            return;
        }

        $flow = new FlowController();
        $zcrmDetails = $flow->get(['id' => $integrationID]);

        if (is_wp_error($zcrmDetails)) {
            return;
        }
        $newDetails = json_decode($zcrmDetails[0]->flow_details);

        $newDetails->tokenDetails = $tokenDetails;
        if (!empty($others['modules'])) {
            $newDetails->default->modules = $others['modules'];
        }
        if (!empty($others['layouts']) && !empty($others['queryModule'])) {
            $newDetails->default->layouts->{$others['queryModule']} = $others['layouts'];
        }

        $flow->update($integrationID, ['flow_details' => \json_encode($newDetails)]);
    }
}
