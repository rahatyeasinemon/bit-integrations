<?php

namespace BitApps\BTCBI\Http\Services\Actions\Notion;

use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BitApps\BTCBI\Http\Services\Actions\Notion\RecordApiHelper;
use BTCBI\Deps\BitApps\WPKit\Http\Response;
use WP_Error;

class NotionController
{
    private $baseurl = "https://api.notion.com/v1/";

    public function authorization($requestParams)
    {
        if (empty($requestParams->clientId) || empty($requestParams->clientSecret) || empty($requestParams->code) || empty($requestParams->redirectURI)) {
            return Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $body = [
          "redirect_uri"  => urldecode($requestParams->redirectURI),
          "grant_type"    => "authorization_code",
          "code"          => $requestParams->code
        ];
        $apiEndpoint = "{$this->baseurl}oauth/token";

        $clientId = $requestParams->clientId;
        $clientSecret = $requestParams->clientSecret;

        $header["Content-Type"] = 'application/json';
        $header["Authorization"] =  'Basic ' . base64_encode("$clientId:$clientSecret");

        $apiResponse = Http::request($apiEndpoint, 'Post', json_encode($body), $header);
        if (is_wp_error($apiResponse) || !empty($apiResponse->error)) {
            Response::error(empty($apiResponse->error_description) ? 'Unknown' : $apiResponse->error_description, 400);
        }
        $apiResponse->generates_on = \time();

        return Response::success($apiResponse);
    }

    public function getAllDatabaseLists($requestParams)
    {

        if (empty($requestParams->accessToken)) {
            Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }
        $apiEndpoint = "{$this->baseurl}search";
        $headers = [
          'Authorization' => "Bearer " . $requestParams->accessToken,
          'Notion-Version' => '2021-08-16'
        ];
        $response = Http::request($apiEndpoint, 'Post', null, $headers);
        if ($response->Error !== null) {
            Response::error(
                __(
                    'Invalid token',
                    'bit-integrations'
                ),
                400
            );
        }
        return Response::success($response);
    }

    public function getFieldsProperties($requestParams)
    {

        if (empty($requestParams->accessToken)) {
            Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }
        $apiEndpoint = "{$this->baseurl}databases/{$requestParams->databaseId}";
        $headers = [
          'Authorization' => "Bearer " . $requestParams->accessToken,
          'Notion-Version' => '2021-08-16'
        ];
        $response = Http::request($apiEndpoint, 'Get', null, $headers);
        if ($response->Error !== null) {
            Response::error(
                __(
                    'Invalid token',
                    'bit-integrations'
                ),
                400
            );
        }
        return Response::success($response);
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId = $integrationData->id;
        $databaseId = $integrationDetails->databaseId;
        $notionFields = $integrationDetails->notionFields;
        $tokenDetails = $integrationDetails->tokenDetails;
        $accessToken = $tokenDetails->access_token;
        $tokenType = $tokenDetails->token_type;
        $field_map = $integrationDetails->field_map;

        if (
            empty($field_map) || empty($accessToken)
        ) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for notion api', 'bit-integrations'));
        }

        $recordApiHelper = new RecordApiHelper($integrationDetails, $integId);


        $notionApiResponse = $recordApiHelper->execute(
            $databaseId,
            $accessToken,
            $tokenType,
            $notionFields,
            $fieldValues,
            $field_map,
        );


        if (is_wp_error($notionApiResponse)) {
            return $notionApiResponse;
        }
        return $notionApiResponse;
    }
}
